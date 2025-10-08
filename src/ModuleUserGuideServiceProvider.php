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

        // $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        Route::middleware('web')->group(__DIR__.'/routes/web.php');

        $this->loadViewsFrom(__DIR__.'/resources/views', 'moduleuserguide');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/moduleuserguide'),
        ], 'views');
        Gate::policy(Module::class, ModulePolicy::class);
    }

    public function register() {}
}
