<?php
use ModuleUserGuide\Http\Controllers\ModuleController;
use ModuleUserGuide\Http\Controllers\UserGuideController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

Route::prefix('module-user-guide')->group(function () {
    Route::resource('modules', ModuleController::class);
    Route::resource('user-guides', UserGuideController::class);
});


Route::get('plugin-assets/{path}', function ($path) {
    $file = base_path('mariselvanzaigo/laravel-user-guide-plugin/moduleuserguide/Resources/assets/' . $path);

    if (!File::exists($file)) {
        abort(404);
    }

    $mimeType = File::mimeType($file);
    return Response::file($file, ['Content-Type' => $mimeType]);
})->where('path', '.*');
