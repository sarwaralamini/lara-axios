<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'api.v1.'], function () {
    Route::post('create/token', [AuthController::class, 'createToken'])->name('create-token');

    Route::middleware('auth:sanctum')->group(function () {
        //
    });
});
