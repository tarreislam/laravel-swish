<?php

namespace Tarre\Swish\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class SwishServiceProvider extends BaseServiceProvider
{

    public function register()
    {

    }

    public function boot()
    {
        /*
         * Publish config
         */
        $this->publishes([
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'swish.php' => config_path('swish.php')
        ], 'laravel-swish');
    }
}