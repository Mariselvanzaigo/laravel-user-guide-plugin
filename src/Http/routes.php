<?php
use ModuleUserGuide\Http\Controllers\ModuleController;
use ModuleUserGuide\Http\Controllers\UserGuideController;
use Illuminate\Support\Facades\Route;

Route::prefix('module-user-guide')->group(function () {
    Route::resource('modules', ModuleController::class);
    Route::resource('user-guides', UserGuideController::class);
});
