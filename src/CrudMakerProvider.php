<?php

/**
 * ServiceProvider
 * User: Administrator
 * Date: 2017/7/3
 * Time: 15:37
 */
namespace Louis\CrudMaker;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

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
            __DIR__ . '/Template' => base_path('resources/crud'),
            __DIR__ . '/config.php' => base_path('config/crud.php'),
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
        | Register the Commands
        |--------------------------------------------------------------------------
        */

        $this->commands([
            \Louis\CrudMaker\Console\CrudMaker::class,
        ]);
    }
}
