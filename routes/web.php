<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->middleware('redirect.auth')->name('home');

// Route::get('dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');

Route::get('dashboard', function () {
    return view('dashboard');
})->middleware('web.auth')->name('dashboard');

Route::get('user/create', function () {
    return view('create');
})->middleware('web.auth')->name('user.create');
