<?php

namespace SanthoshKorukonda\Artificer\Bootstrap;

# Import Laravel Blade
use Illuminate\Support\Facades\Blade;
# Import Laravel Collective Form
use Collective\Html\FormFacade as Form;
# Import Laravel base Service Provider
use Illuminate\Support\ServiceProvider;
# Import Artificer essentials to register
use SanthoshKorukonda\Artificer\Artificer;
use SanthoshKorukonda\Artificer\Console\FormClearCommand;
use SanthoshKorukonda\Artificer\Contracts\Artificer as ArtificerContract;

class ArtificerServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/../components', 'artificer');

        # Publish Fartisan views to resources/views/vendor directory
        $this->publishes([
            __DIR__.'/../components' => resource_path('views/vendor/artificer/components'),
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
        # Register Artificer implementation with ArtificerContract(interface)
        $this->app->singleton(ArtificerContract::class, function ($app) {
            return new Artificer;
        });

        # Register Artificer alias into the application. It is used in resolving
        # artificer implementaion out of the application. Ex: app('artificer') or 
        # app(FartisanContract::class)
        $this->app->alias(ArtificerContract::class, "artificer");
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ArtificerContract::class, "artificer"];
    }

    /**
     * Register some basic form components of Fartisan.
     *
     * @return void
     */
    protected function bootFormComponents()
    {
        # Register bootstrap text component
        Form::component('bsText', 'artificer::text', [
            'options' => (object) []
        ]);

        # Register bootstrap file component
        Form::component('bsFile', 'artificer::file', [
            'options' => (object) []
        ]);

        # Register bootstrap text component with input group
        Form::component('bsInputGroup', 'artificer::inputgroup', [
            'options' => (object) []
        ]);

        # Register bootstrap select component with list. i.e, select options
        # are provided in the json itself
        Form::component('bsSelectWithList', 'artificer::selectwithlist', [
            'options' => (object) []
        ]);

        # Register bootstrap select component with database. i.e, select options
        # are fetched from the database
        Form::component('bsSelectWithDb', 'artificer::selectwithdb', [
            'options' => (object) []
        ]);

        # Register bootstrap text component with feedback icon
        Form::component('bsTextFeedback', 'artificer::textfeedback', [
            'options' => (object) []
        ]);

        # Register bootstrap checkbox component
        Form::component('bsCheckbox', 'artificer::checkbox', [
            'options' => (object) []
        ]);

        # Register bootstrap checkbox component which displays inline
        Form::component('bsCheckboxInline', 'artificer::checkboxinline', [
            'options' => (object) []
        ]);

        # Register bootstrap radio button component
        Form::component('bsRadio', 'artificer::radio', [
            'options' => (object) []
        ]);

        # Register bootstrap button component 
        Form::component('bsButton', 'artificer::button', [
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
            $filePath = storage_path("artificer/views/$expression.php");
            return "<?php include_once \"$filePath\" ?>";
        });
    }
}
