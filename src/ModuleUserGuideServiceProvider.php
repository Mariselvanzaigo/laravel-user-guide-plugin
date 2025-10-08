<?php
namespace ModuleUserGuide;

use Illuminate\Support\ServiceProvider;

class ModuleUserGuideServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load plugin routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Load plugin views with namespace 'moduleuserguide'
        $this->loadViewsFrom(__DIR__.'/resources/views', 'moduleuserguide');

        // Load plugin migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Publish views to allow customization
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/moduleuserguide'),
        ], 'views');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // You can bind additional services here if needed
    }
}
