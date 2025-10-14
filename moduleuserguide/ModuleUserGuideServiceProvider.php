<?php
namespace ModuleUserGuide;

use Illuminate\Support\Facades\Gate;
use ModuleUserGuide\Models\Module;
use ModuleUserGuide\Policies\ModulePolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

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

        // Publish assets (JS/CSS)
        $this->publishes([
            __DIR__.'/Resources/assets' => public_path('vendor/moduleuserguide'),
        ], 'public');

        // Register policies
        Gate::policy(Module::class, ModulePolicy::class);

        // ----------------------
        // Pass user role only to plugin views
        // ----------------------
        $this->app->booted(function () {
            View::composer('moduleuserguide::*', function ($view) {
                $userRoleId = null;

                // Safely get authenticated user
                if (Auth::check()) {
                    $user = Auth::user();
                    $userRoleId = DB::table('role_user')
                        ->where('user_id', $user->id)
                        ->value('role_id');
                }

                // Pass only to plugin views
                $view->with('userRoleId', $userRoleId);
            });
        });
    }


    public function register()
    {
        //
    }
}
