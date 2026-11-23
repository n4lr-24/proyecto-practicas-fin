<?php

use App\Http\Controllers\Practica1_5\ComputadoraController;
use App\Http\Controllers\Practica1_5\SolicitudReservaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('equipos')->name('equipos.')->group(function () {
        Route::get('/', [ComputadoraController::class, 'index'])
            ->middleware('rol:administrador,estudiante')
            ->name('index');

        Route::middleware('rol:administrador')->group(function () {
            Route::get('/nuevo', [ComputadoraController::class, 'create'])->name('create');
            Route::post('/', [ComputadoraController::class, 'store'])->name('store');
            Route::get('/{computadora}/editar', [ComputadoraController::class, 'edit'])->name('edit');
            Route::put('/{computadora}', [ComputadoraController::class, 'update'])->name('update');
            Route::delete('/{computadora}', [ComputadoraController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('reservaciones')->name('reservas.')->middleware('rol:administrador,estudiante')->group(function () {
        Route::get('/', [SolicitudReservaController::class, 'index'])->name('index');
        Route::post('/', [SolicitudReservaController::class, 'store'])
            ->middleware('rol:estudiante')
            ->name('store');
        Route::get('/{reserva}', [SolicitudReservaController::class, 'show'])->name('show');
        Route::patch('/{reserva}/cancelar', [SolicitudReservaController::class, 'cancelar'])->name('cancelar');
    });
});
