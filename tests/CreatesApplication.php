<?php

namespace Tests;


use Illuminate\Config\Repository;
use Tarre\Swish\Providers\SwishServiceProvider;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = new \Illuminate\Foundation\Application(
            $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
        );
        /*
                $app->singleton(
                    \Illuminate\Contracts\Http\Kernel::class,
                    \App\Http\Kernel::class
                );

                $app->singleton(
                    \Illuminate\Contracts\Console\Kernel::class,
                    \App\Console\Kernel::class
                );

                $app->singleton(
                    \Illuminate\Contracts\Debug\ExceptionHandler::class,
                    \App\Exceptions\Handler::class
                );

        */
        /*
         * Load default config for tests
         */
        $items = include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'swish.php';

        /*
         * Create config repository
         */
        $repository = new Repository([
            // key emulates the file as if it was in config\x
            'swish' => $items
        ]);

        /*
         * Initialize the config
         */
        $app->instance('config', $repository);

        /*
         * Load service provider
         */
        $app->register(SwishServiceProvider::class);
        // $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}