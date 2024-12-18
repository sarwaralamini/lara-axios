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
});
