<?php
use ModuleUserGuide\Http\Controllers\ModuleController;
use ModuleUserGuide\Http\Controllers\UserGuideController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

Route::prefix('admin')->group(function () {
    Route::prefix('module-user-guide')->group(function () {
        Route::resource('user_guide_modules', ModuleController::class);
        Route::resource('user-guides', UserGuideController::class);
        Route::get('user-guides/show', [UserGuideController::class, 'show'])
            ->name('user-guides.show');

    });
});
Route::prefix('module-user-guide')->group(function () {
    Route::resource('user_guide_modules', ModuleController::class);
    Route::resource('user-guides', UserGuideController::class);
    Route::get('user-guides/show', [UserGuideController::class, 'show'])
        ->name('user-guides.show');

});


Route::get('plugin-assets/{path}', function ($path) {
    $file = base_path('vendor/moduleuserguide/laravel-user-guide-plugin/moduleuserguide/Resources/assets/' . $path);

    if (!File::exists($file)) {
        abort(404);
    }

    $mimeType = File::mimeType($file);
    return Response::file($file, ['Content-Type' => $mimeType]);
})->where('path', '.*');
