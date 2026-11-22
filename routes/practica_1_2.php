<?php

use App\Http\Controllers\Practica1_2\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('directorio-usuarios')->name('usuarios.')->group(function () {
    Route::middleware('rol:administrador')->group(function () {
        Route::get('/papelera/eliminados', [UsuarioController::class, 'papelera'])->name('papelera');
        Route::patch('/{id}/restaurar', [UsuarioController::class, 'restaurar'])->name('restaurar');
        Route::delete('/{id}/borrar-definitivo', [UsuarioController::class, 'forceDelete'])->name('forceDelete');
    });

    Route::middleware('rol:administrador,empleado')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::get('/nuevo', [UsuarioController::class, 'create'])->name('create');
        Route::post('/', [UsuarioController::class, 'store'])->name('store');
        Route::get('/{usuario}', [UsuarioController::class, 'show'])->name('show');
        Route::get('/{usuario}/editar', [UsuarioController::class, 'edit'])->name('edit');
        Route::put('/{usuario}', [UsuarioController::class, 'update'])->name('update');
    });

    Route::delete('/{usuario}', [UsuarioController::class, 'destroy'])
        ->middleware('rol:administrador')
        ->name('destroy');
});
