<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'api.v1.'], function () {
    Route::post('create/token', [AuthController::class, 'createToken'])->name('create-token');
    Route::post('web/login', [AuthController::class, 'WebLogin'])->name('web-login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('app/logout', [AuthController::class, 'appLogout'])->name('app-logout');
        Route::get('web/logout', [AuthController::class, 'WebLogout'])->name('web-logout');
    });
});
