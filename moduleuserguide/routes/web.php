<?php
use ModuleUserGuide\Http\Controllers\ModuleController;
use ModuleUserGuide\Http\Controllers\UserGuideController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
Route::middleware(['web', 'auth'])->group(function () {
$prefix = request()->segment(1) ?? 'default'; 
Route::prefix($prefix . '/module-user-guide')
    ->name($prefix . '.module-user-guide.') // dynamic route names
    ->group(function () {

        // Resource routes
        Route::resource('user_guide_modules', ModuleController::class);
        Route::resource('user-guides', UserGuideController::class);

        // Optional custom show
        Route::get('user-guides/show', [UserGuideController::class, 'show'])
        ->name('user-guides.display');

        // CKEditor image upload
        Route::post('user-guides/upload-image', [UserGuideController::class, 'uploadImage'])
            ->name('user-guides.upload-image');
    });
});


Route::get('moduleuserguide/{path}', function ($path) {
    $file = base_path('vendor/moduleuserguide/laravel-user-guide-plugin/moduleuserguide/Resources/assets/' . $path);

    if (!File::exists($file)) {
        abort(404);
    }

    $mimeType = File::mimeType($file);
    return Response::file($file, ['Content-Type' => $mimeType]);
})->where('path', '.*');
