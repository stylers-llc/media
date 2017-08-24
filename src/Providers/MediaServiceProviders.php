<?php
/**
 * Created by PhpStorm.
 * User: sty021
 * Date: 2017.08.24.
 * Time: 11:27
 */

namespace Stylers\Media\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MediaServiceProviders extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('media.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../../config/config.php', 'media'
        );
    }

    protected function publishDatabase()
    {
        $this->publishes([
            __DIR__ . '/../../database/Migrations/' => database_path('/migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../../database/Seeders/' => database_path('/seeds')
        ], 'seeds');
    }

    protected function bootRoutes()
    {
        $this->app->booted(function () {
            require __DIR__ . '/../routes.php';
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->publishDatabase();
        $this->bootRoutes();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {}
}
