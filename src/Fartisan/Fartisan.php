<?php

namespace SanthoshKorukonda\Fartisan;

# Import stdClass
use stdClass;
# Import Carbon
use Carbon\Carbon;
# Import HtmlString
use Illuminate\Support\HtmlString;
# Import DB, Cache and Storage
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
# Import Laravel Collective Form
use Collective\Html\FormFacade as Form;
# Import Fartisan contract
use SanthoshKorukonda\Fartisan\Contracts\Fartisan as FartisanContract;

class Fartisan implements FartisanContract
{
    /**
     * Build html form from the given json schema and cache it.
     *
     * @param  string  $schema
     * @param  int  $index
     * @return array
     */
    public function build(string $schema, int $index = 0)
    {
        # Calculate sha1 hash of the given json schema, so it acts as an unique identifier
        $hash = sha1($schema);
        # Decode form schema from the given json
        $schema = json_decode($schema);

        # Find whether this schema's cached code already exists
        $exists = Storage::disk('fartisan')->exists("$hash.php");

        # If no, then create the form from the json schema and cache it
        if (! $exists) {
            $this->buildCachedForm($hash, $schema[$index]);
        }

        # Now return hash and any db cached data
        return array_merge([
            "filename" => $hash
        ], $this->extractDatabaseData($hash, $schema[$index]));
    }

    /**
     * Build raw html form from the given json schema and "do not" cache it.
     *
     * @param  string  $schema
     * @param  string|null  $values
     * @param  int  $index
     * @return HtmlString
     */
    public function buildHtml(string $schema, string $values = null, int $index = 0)
    {
        # Calculate sha1 hash of the given json schema, so it acts as an unique identifier
        $hash = sha1($schema);
        # Decode form schema from given json
        $schema = json_decode($schema)[$index];

        # Check whether values is null or not
        if ($values) {
            $values = json_decode($values)[$index];
        } else {
            $values = new stdClass;
        }

        # Now build raw html form with values if given any, and return it
        return $this->buildForm($hash, $schema, $values);
    }

    /**
     * Extract database data of the form from the cache.
     *
     * @param  string  $hash
     * @param  stdClass  $schema
     * @return array
     */
    protected function extractDatabaseData(string $hash, stdClass $schema)
    {
        # Prepare expiry time for the cached data
        $expiresAt = Carbon::now()->addWeek();

        # Return cached database data for the json hash
        return Cache::store("fartisan")->remember($hash, $expiresAt, function () use ($schema) {
            # Extract components schema
            $components = $schema->components;
            # Define an empty array to store database data
            $data = [];

            # Loop through database components
            foreach ($components as $component) {
                # If database option exists for the input, then store in data array
                if (! empty($component->options->input->database)) {
                    $config = $component->options->input->database;
                    # Fetch database data for the input and store with uid (defined in json) as key in data array
                    $data[$config->uid] = $this->fetchDatabaseData($config);
                }
            }
            return $data;
        });
    }

    /**
     * Actual form building implementation from json schema.
     *
     * @param  string  $hash
     * @param  stdClass  $schema
     * @param  stdClass  $values
     * @param  bool  $cache
     * @return HtmlString
     */
    protected function buildForm(string $hash, stdClass $schema, stdClass $values, bool $cache = false)
    {
        # Define an empty string to store html
        $htmlString = "";
        # Fetch attributes and components from schema
        $attributes = (array) $schema->attributes;
        $components = $schema->components;

        # Create form opening html with form attributes
        $htmlString .= Form::open($attributes);

        # Check whether this form has to be cached or not
        # if yes, then build cached components for the form
        if ($cache) {
            $htmlString .= $this->buildCachedComponents($hash, $components);
        } else {
            # Build form components without any caching enabled
            $htmlString .= $this->buildComponents($components, $values);
        }

        # Create form closing html and a newline at EOF
        $htmlString .= Form::close() . "\n";

        # Now return html as HtmlString instance
        return new HtmlString($htmlString);
    }

    /**
     * Create a cached form from the given json schema.
     *
     * @param  string  $hash
     * @param  stdClass  $schema
     * @return void
     */
    protected function buildCachedForm(string $hash, stdClass $schema)
    {
        # Define empty values object because cached form "does not" takes any values
        $values = new stdClass;

        # Build cached form from the given schema
        $html = $this->buildForm($hash, $schema, $values, true);

        # Store the html output to the fartisan filesystem
        # so it can served for the future requests
        Storage::disk("fartisan")->put("$hash.php", $html->toHtml());
    }

    /**
     * Build components by mapping given values.
     *
     * @param  array  $components
     * @param  stdClass  $values
     * @return string
     */
    protected function buildComponents(array $components, stdClass $values)
    {
        # Define an empty string to store components html
        $htmlString = "";

        # Loop through components and map given values to the component default value
        foreach ($components as $component) {
            switch ($component->name) {
                case 'bsText':
                    # Normalize component default value
                    $component->options->input->value = $this->normalizeComponentValue($component->options->input->name, $values);
                    break;
                case 'bsSelectWithDb':
                    $component->options->input->value = $this->normalizeComponentValue($component->options->input->name, $values);
                    break;
                case 'bsSelectWithList':
                    $component->options->input->value = $this->normalizeComponentValue($component->options->input->name, $values);
                    break;
            }
            # If the component has a database option (likely selectwithdb),
            # then fetch the database data and assign it to the input list
            if (! empty($component->options->input->database)) {
                # Assign database data to the input list
                $component->options->input->list = $this->fetchDatabaseData($component->options->input->database);
                # Change the component name (selectwithdb to selectwithlist)
                $name = "bsSelectWithList";
                # Unset database option on the component schema
                unset($component->options->input->database);
            } else {
                # If no database option exists, then default name to component name
                $name = $component->name;
            }

            # Create html from the component options and store it
            $htmlString .= Form::$name($component->options);
        }

        # Now return the components html
        return $htmlString;
    }

    /**
     * Normalize component value.
     *
     * @param  string  $name
     * @param  stdClass  $values
     * @return mixed
     */
    protected function normalizeComponentValue(string $name, stdClass $values)
    {
        # If values object has component name, then return value in values object
        if (! empty($values->{$name})) {
            return $values->{$name};
        } else {
            # Or else return "null" as input value
            return null;
        }
    }

    /**
     * Build components and cache its database data.
     *
     * @param  string  $hash
     * @param  array  $components
     * @return string
     */
    protected function buildCachedComponents(string $hash, array $components)
    {
        # Define an empty string to store cached components html
        $htmlString = "";

        # Loop through all components and if any component has database as option,
        # then fetch database data and cache it for later requests
        foreach ($components as $component) {
            # Check if the component has any database options, if yes then cache data
            if (! empty($component->options->input->database)) {
                $this->cacheDatabaseAccess($hash, $component->options->input->database);
            }
            $name = $component->name;
            # Create component html from the component options
            $htmlString .= Form::$name($component->options);
        }

        # Now return components full html
        return $htmlString;
    }

    /**
     * Cache database data for the form.
     *
     * @param  string  $hash
     * @param  stdClass  $config
     * @return void
     */
    protected function cacheDatabaseAccess(string $hash, stdClass $config)
    {
        # Fetch cached data if exists any, or return empty array for the given hash
        $data = Cache::get($hash, []);

        # Store fetched database data into data variable to cache it
        $data[$config->uid] = $this->fetchDatabaseData($config);

        # Prepare expiry time for the cached data
        $expiresAt = Carbon::now()->addWeek();

        # Cache the database data with the newly fetched data
        Cache::store("fartisan")->put($hash, $data, $expiresAt);
    }

    /**
     * Fetch data from database for the given config.
     *
     * @param  stdClass  $config
     * @return array
     */
    protected function fetchDatabaseData(stdClass $config)
    {
        # Fetch database data from the given config
        return DB::table($config->table)
                    ->select($config->columns)
                    ->where((array) $config->where)
                    ->get()
                    ->pluck(...array_reverse($config->columns))
                    ->all();
    }

    /**
     * Build html for <option> tag from the given options.
     *
     * @param  array  $options
     * @return string
     */
    public function buildSelectOptions(array $options)
    {
        # Define an emtpy string to store <option> tags html
        $htmlString = "";

        # Loop through the options and build html
        foreach ($options as $key => $value) {
            $htmlString .= "<option value='$key'>$value</option>\n";
        }

        # Return all <option> html
        return $htmlString;
    }

    /**
     * Return raw php code for building <option> tag in blade template.
     *
     * @param  string  $variable
     * @return string
     */
    public function compileOptions(string $variable)
    {
        return '<?= Fartisan::buildSelectOptions($' . $variable . '); ?>';
    }
}
