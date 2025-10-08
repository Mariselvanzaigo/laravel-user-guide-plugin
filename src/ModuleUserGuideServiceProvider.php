<?php
namespace ModuleUserGuide;

use Illuminate\Support\ServiceProvider;

class ModuleUserGuideServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'moduleuserguide');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/moduleuserguide'),
        ], 'views');
    }

    public function register() {}
}
