<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('panel')
        : redirect()->route('login');
});

Route::get('/panel', function () {
    return view('dashboard');
})->middleware('auth')->name('panel');

require __DIR__.'/practica_1_1.php';
require __DIR__.'/practica_1_2.php';
require __DIR__.'/practica_1_3.php';
require __DIR__.'/practica_1_4.php';
