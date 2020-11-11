<?php

namespace Tests;


use Illuminate\Broadcasting\BroadcastServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Queue\QueueServiceProvider;
use Tarre\Swish\Providers\SwishServiceProvider;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        $app = new Application(
            $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
        );
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
         * Load required service providers
         */
        $app->register(SwishServiceProvider::class);
        $app->register(BroadcastServiceProvider::class);
        $app->register(QueueServiceProvider::class);
        // $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}