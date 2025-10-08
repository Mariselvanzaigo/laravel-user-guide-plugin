<?php
namespace ModuleUserGuide;

use Illuminate\Support\Facades\Gate;
use ModuleUserGuide\Models\Module;
use ModuleUserGuide\Policies\ModulePolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleUserGuideServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load web routes
        Route::middleware('web')->group(__DIR__.'/routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'moduleuserguide');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Publish views to allow customization
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/moduleuserguide'),
        ], 'views');

        // Publish assets (JS/CSS) to public/vendor/moduleuserguide
        $this->publishes([
            __DIR__.'/Resources/assets' => public_path('vendor/moduleuserguide'),
        ], 'public');

        // Register policies
        Gate::policy(Module::class, ModulePolicy::class);
    }

    public function register()
    {
        //
    }
}
