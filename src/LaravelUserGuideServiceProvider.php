<?php

namespace LaravelUserGuide;

use Illuminate\Support\ServiceProvider;

class LaravelUserGuideServiceProvider extends ServiceProvider
{
    public function boot()
    {
        \->loadRoutesFrom(__DIR__.'/Http/routes.php');
        \->loadViewsFrom(__DIR__.'/Resources/views', 'userguide');
        \->loadMigrationsFrom(__DIR__.'/Database/migrations');
        \->publishes([
            __DIR__.'/Resources/assets' => public_path('vendor/userguide'),
        ], 'userguide-assets');
    }

    public function register()
    {
        //
    }
}
