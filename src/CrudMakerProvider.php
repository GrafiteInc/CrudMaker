<?php

namespace Grafite\CrudMaker;

use Grafite\FormMaker\FormMakerProvider;
use Illuminate\Support\ServiceProvider;

class CrudMakerProvider extends ServiceProvider
{
    /**
     * Boot method.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Templates/Laravel' => base_path('resources/crudmaker'),
            __DIR__.'/../config/crudmaker.php' => base_path('config/crudmaker.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        /*
        |--------------------------------------------------------------------------
        | Providers
        |--------------------------------------------------------------------------
        */

        if (class_exists('Illuminate\Foundation\AliasLoader')) {
            $this->app->register(FormMakerProvider::class);
        }

        /*
        |--------------------------------------------------------------------------
        | Register the Commands
        |--------------------------------------------------------------------------
        */

        $this->commands([
            \Grafite\CrudMaker\Console\CrudMaker::class,
            \Grafite\CrudMaker\Console\TableCrudMaker::class,
        ]);
    }
}
