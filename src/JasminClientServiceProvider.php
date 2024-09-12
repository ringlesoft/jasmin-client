<?php

namespace RingleSoft\JasminClient;

use Illuminate\Support\ServiceProvider;

class JasminClientServiceProvider extends ServiceProvider
{


    public function register(): void
    {
        $this->app->alias(Facades\JasminClient::class, 'JasminClient');
    }

    public function boot(): void
    {

        $this->publishItems();

        $this->mergeConfigFrom(
            __DIR__ . '/../config/jasmin_client.php', 'jasmin_client'
        );

        if ($this->app->runningInConsole()) {
//            $this->commands([]);
        }
    }

    private function publishItems(): void
    {
        if (!function_exists('config_path') || !$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/jasmin_client.php' => config_path('jasmin_client.php'),
        ], 'jasmin-client-config');
    }
}
