<?php

namespace mrcrmn\Http2Push;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use mrcrmn\Http2Push\Middleware\AttachPushHeader;

class Http2PushServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mrcrmn');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'mrcrmn');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Registers the Blade directive.
        Blade::directive('preload', function($arguments) {
            return "<?php echo preload($arguments, false); ?>";
        });

        // Pushes the middleware to the web group.
        $this->app['router']->pushMiddlewareToGroup('web', AttachPushHeader::class);

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/http2push.php', 'http2push');

        // Register the service the package provides.
        $this->app->singleton('http2push', function ($app) {
            return new Http2Push(config('http2push.preload'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['http2push'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/http2push.php' => config_path('http2push.php'),
        ], 'http2push.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/mrcrmn'),
        ], 'http2push.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/mrcrmn'),
        ], 'http2push.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/mrcrmn'),
        ], 'http2push.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
