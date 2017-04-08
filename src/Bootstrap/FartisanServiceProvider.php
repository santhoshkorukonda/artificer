<?php

namespace SanthoshKorukonda\Fartisan\Bootstrap;

# Import Laravel Blade
use Illuminate\Support\Facades\Blade;
# Import Laravel Collective Form
use Collective\Html\FormFacade as Form;
# Import Laravel base Service Provider
use Illuminate\Support\ServiceProvider;
# Import Fartisan essentials to register
use SanthoshKorukonda\Fartisan\Fartisan;
use SanthoshKorukonda\Fartisan\Console\FormClearCommand;
use SanthoshKorukonda\Fartisan\Contracts\Fartisan as FartisanContract;

class FartisanServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var  bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        # Boot Fartisan form clear command
        if ($this->app->runningInConsole()) {

            $this->commands([
                FormClearCommand::class
            ]);
        }

        # Register Fartisan components views into the application
        $this->loadViewsFrom(__DIR__.'/../components', 'fartisan');

        # Publish Fartisan views to resources/views/vendor directory
        $this->publishes([
            __DIR__.'/../components' => resource_path('views/vendor/fartisan/components'),
        ]);

        # Boot Fartisan form components
        $this->bootFormComponents();

        # Boot Fartisan blade directive
        $this->bootFormBladeDirective();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        # Register Fartisan implementation with FartisanContract(interface)
        $this->app->singleton(FartisanContract::class, function ($app) {
            return new Fartisan;
        });

        # Register Fartisan alias into the application. It is used in resolving
        # fartisan implementaion out of the application. Ex: app('fartisan') or 
        # app(FartisanContract::class)
        $this->app->alias(FartisanContract::class, "fartisan");
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [FartisanContract::class, "fartisan"];
    }

    /**
     * Register some basic form components of Fartisan.
     *
     * @return void
     */
    protected function bootFormComponents()
    {
        # Register bootstrap text component
        Form::component('bsText', 'fartisan::text', [
            'options' => (object) []
        ]);

        # Register bootstrap file component
        Form::component('bsFile', 'fartisan::file', [
            'options' => (object) []
        ]);

        # Register bootstrap text component with input group
        Form::component('bsInputGroup', 'fartisan::inputgroup', [
            'options' => (object) []
        ]);

        # Register bootstrap select component with list. i.e, select options
        # are provided in the json itself
        Form::component('bsSelectWithList', 'fartisan::selectwithlist', [
            'options' => (object) []
        ]);

        # Register bootstrap select component with database. i.e, select options
        # are fetched from the database
        Form::component('bsSelectWithDb', 'fartisan::selectwithdb', [
            'options' => (object) []
        ]);

        # Register bootstrap text component with feedback icon
        Form::component('bsTextFeedback', 'fartisan::textfeedback', [
            'options' => (object) []
        ]);

        # Register bootstrap checkbox component
        Form::component('bsCheckbox', 'fartisan::checkbox', [
            'options' => (object) []
        ]);

        # Register bootstrap checkbox component which displays inline
        Form::component('bsCheckboxInline', 'fartisan::checkboxinline', [
            'options' => (object) []
        ]);

        # Register bootstrap radio button component
        Form::component('bsRadio', 'fartisan::radio', [
            'options' => (object) []
        ]);

        # Register bootstrap button component 
        Form::component('bsButton', 'fartisan::button', [
            'options' => (object) []
        ]);
    }

    /**
     * Register a blade directive for the Fartisan.
     *
     * @return void
     */
    protected function bootFormBladeDirective()
    {
        Blade::directive('includeJsonForm', function ($expression) {
            $filePath = storage_path("fartisan/views/$expression.php");
            return "<?php include_once \"$filePath\" ?>";
        });
    }
}
