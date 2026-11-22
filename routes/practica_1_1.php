<?php

use App\Http\Controllers\Practica1_1\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.procesar');

    Route::get('/admin/verificacion-otp', [AuthController::class, 'mostrarOtp'])
        ->middleware('signed')
        ->name('admin.otp.vista');

    Route::post('/admin/verificacion-otp', [AuthController::class, 'verificarOtp'])
        ->middleware('signed')
        ->name('admin.otp.verificar');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
