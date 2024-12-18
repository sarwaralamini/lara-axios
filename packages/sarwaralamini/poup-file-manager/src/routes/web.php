<?php

use Illuminate\Support\Facades\Route;
use Sarwar\PopupFileManager\Http\Controllers\FileManagerController;

Route::group([
    'prefix' => 'sarwar/popup-file-manager',
    'middleware' => ['web','web.auth']
    ], function () {
    Route::get('demo', function () {
        return view('vendor.popup-file-manager');
    });
    Route::get('/files', [FileManagerController::class, 'getFiles'])->name('file-manager.files');
    Route::post('/upload', [FileManagerController::class, 'uploadFile'])->name('file-manager.upload');
    Route::post('/create-directory', [FileManagerController::class, 'createDirectory'])->name('file-manager.create-directory');
    Route::post('/delete-multiple', [FileManagerController::class, 'deleteMultiple'])->name('file-manager.delete-multiple');
});
