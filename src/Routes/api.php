<?php

use Illuminate\Support\Facades\Route;
use LechugaNegra\SettingManager\Http\Controllers\SettingController;

Route::prefix('api/settings')->name('api.settings.')->group(function () {
    Route::get('/{module}', [SettingController::class, 'getByModule'])->name('getByModule');
    Route::get('/{module}/{key}', [SettingController::class, 'get'])->name('get');
    Route::put('/{module}', [SettingController::class, 'updateByModule'])->name('updateByModule');
    Route::put('/{module}/{key}', [SettingController::class, 'update'])->name('update');
});
