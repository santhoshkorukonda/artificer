<?php

namespace SanthoshKorukonda\Artificer\Tests;

use App\City;
// use Orchestra\Testbench\TestCase;
use Tests\TestCase;
use Collective\Html\FormFacade;
use Illuminate\Support\HtmlString;
use Collective\Html\HtmlServiceProvider;
use SanthoshKorukonda\Artificer\Bootstrap\Artificer;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\Factory as ModelFactory;
use SanthoshKorukonda\Artificer\Bootstrap\ArtificerServiceProvider;

class ArtificerTest extends TestCase
{
    use DatabaseTransactions,
        DatabaseMigrations;

    protected function getPackageProviders($app)
    {
        return [
            HtmlServiceProvider::class,
            ArtificerServiceProvider::class
        ];
    }
    protected function getPackageAliases($app)
    {
        return [
            'Artificer' => Artificer::class,
            'Form' => FormFacade::class,
            'Html' => FormFacade::class
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        # Setup cache store for Artificer
        $app['config']->set('cache.stores.artificer', [
            'driver' => 'file',
            'path' => storage_path('artificer/cache'),
        ]);

        # Setup Filesystems disk for Artificer
        $app['config']->set('filesystems.disks.artificer', [
            'driver' => 'local',
            'root' => storage_path('artificer/views'),
        ]);

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /** @test */
    public function it_should_return_an_array_with_cached_form_filename()
    {
        factory(City::class, 10)->create([
            "StateId" => 1403
        ]);

        $schema = json_decode($this->getDummyJson())[0];
        $data = Artificer::build($schema);

        $this->assertArrayHasKey("filename", $data);
        $filename = $data["filename"] . ".php";
        $this->assertFileExists(storage_path("artificer/views/$filename"));
    }

    /** @test */
    public function it_should_return_an_instance_of_illuminate_htmlstring()
    {
        factory(City::class, 10)->create([
            "StateId" => 1403
        ]);

        $schema = json_decode($this->getDummyJson())[0];
        $data = Artificer::buildHtml($schema);

        $this->assertInstanceOf(HtmlString::class, $data);
    }

    /** @test */
    public function it_should_return_a_string_of_php_syntax()
    {
        $filename = sha1("hash") . ".php";

        $expected = '<?= Artificer::buildSelectOptions($' . $filename . '); ?>';
        $actual = Artificer::compileOptions($filename);

        $this->assertSame($expected, $actual);
    }

    /** @test */
    public function it_should_return_a_string_of_html_syntax()
    {
        $options = ["Hyderabad", "Visakhapatnam", "Amaravati"];

        $htmlString = "";
        foreach ($options as $key => $option) {
            $htmlString .= "<option value='$key'>$option</option>\n";
        }
        $actual = Artificer::buildSelectOptions($options);

        $this->assertSame($htmlString, $actual);
    }

    protected function getDummyJson()
    {
        return '[{"attributes":{"url":"foo/bar","method":"POST","files":true,"id":"Enquiry","class":"form"},"components":[{"name":"bsText","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Email","text":"Email*","attributes":[]},"input":{"name":"Email","value":null,"attributes":[]}}},{"name":"bsFile","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Image","text":"Image*","attributes":[]},"input":{"name":"Image","value":null,"attributes":[]}}},{"name":"bsSelectWithDb","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Units","text":"Units*","attributes":[]},"input":{"name":"Units","value":null,"attributes":[],"database":{"table":"Cities","columns":["Id","Name"],"where":{"StateId":1403},"uid":"Cities"}}}},{"name":"bsCheckbox","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Image","text":"Image*","attributes":[]},"input":[{"name":"Email","text":"Male","value":"Male","checked":false,"attributes":[]},{"name":"Email","text":"Female","value":"Female","checked":true,"attributes":[]}]}},{"name":"bsRadio","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Image","text":"Image*","attributes":[]},"input":[{"name":"Email","text":"Male","value":"Male","checked":false,"attributes":[]},{"name":"Email","text":"Female","value":"Female","checked":true,"attributes":[]}]}},{"name":"bsButton","options":{"row":{"start":true,"end":false},"wrapper":{"start":true,"end":false,"options":{"class":"col-md-4","attributes":[]}},"input":{"value":"Submit","attributes":{"name":"Submit","type":"submit","class":"btn btn-primary"}}}},{"name":"bsButton","options":{"row":{"start":false,"end":true},"wrapper":{"start":false,"end":true,"options":{"class":"col-md-4","attributes":[]}},"input":{"value":"Reset","attributes":{"name":"Reset","type":"reset","class":"btn btn-default"}}}}]}]';
    }
}