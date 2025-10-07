<?php

use LaravelUserGuide\Http\Controllers\ModuleController;
use LaravelUserGuide\Http\Controllers\UserGuideController;

Route::prefix('user-guide')->group(function () {
    Route::resource('modules', ModuleController::class);
    Route::resource('guides', UserGuideController::class);
});
