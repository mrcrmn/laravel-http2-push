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
    }
}
