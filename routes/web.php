<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileManagerController;

Route::get('/', function () {
    return view('welcome');
})->middleware('redirect.auth')->name('home');

Route::middleware('web.auth')->group(function () {
    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('user/create', function () {
        return view('create');
    })->name('user.create');

    Route::get('/file-manager/files', [FileManagerController::class, 'getFiles'])->name('file-manager.files');
    Route::post('/file-manager/upload', [FileManagerController::class, 'uploadFile'])->name('file-manager.upload');
    Route::post('/file-manager/create-directory', [FileManagerController::class, 'createDirectory'])->name('file-manager.create-directory');
    Route::post('/file-manager/delete-multiple', [FileManagerController::class, 'deleteMultiple'])->name('file-manager.delete-multiple');
});
