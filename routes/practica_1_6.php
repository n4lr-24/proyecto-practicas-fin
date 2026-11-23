<?php

use App\Http\Controllers\Practica1_6\RegistroJugadorController;
use App\Http\Controllers\Practica1_6\SalaJuegoController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/crear-cuenta', [RegistroJugadorController::class, 'create'])->name('registro.vista');
    Route::post('/crear-cuenta', [RegistroJugadorController::class, 'store'])->name('registro.guardar');
});

Route::middleware(['auth', 'rol:administrador,jugador'])->prefix('juego-gato')->name('gato.')->group(function () {
    Route::get('/', [SalaJuegoController::class, 'index'])->name('index');
    Route::get('/historial', [SalaJuegoController::class, 'historial'])->name('historial');

    Route::middleware('rol:jugador')->group(function () {
        Route::post('/', [SalaJuegoController::class, 'store'])->name('store');
        Route::post('/{sala}/entrar', [SalaJuegoController::class, 'entrar'])->name('entrar');
        Route::post('/{sala}/jugar', [SalaJuegoController::class, 'jugar'])->name('jugar');
        Route::post('/{sala}/rendirse', [SalaJuegoController::class, 'rendirse'])->name('rendirse');
    });

    Route::patch('/{sala}/cancelar', [SalaJuegoController::class, 'cancelar'])
        ->middleware('rol:administrador')
        ->name('cancelar');

    Route::get('/{sala}/estado', [SalaJuegoController::class, 'estado'])->name('estado');
    Route::get('/{sala}', [SalaJuegoController::class, 'show'])->name('show');
});
