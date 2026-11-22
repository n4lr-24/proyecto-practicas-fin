<?php

use App\Http\Controllers\Practica1_3\CalificacionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('control-escolar')->name('calificaciones.')->group(function () {
    Route::get('/', [CalificacionController::class, 'index'])
        ->middleware('rol:profesor,alumno')
        ->name('index');

    Route::middleware('rol:profesor')->group(function () {
        Route::get('/nuevo', [CalificacionController::class, 'create'])->name('create');
        Route::post('/', [CalificacionController::class, 'store'])->name('store');
        Route::get('/{calificacion}/editar', [CalificacionController::class, 'edit'])->name('edit');
        Route::put('/{calificacion}', [CalificacionController::class, 'update'])->name('update');
        Route::delete('/{calificacion}', [CalificacionController::class, 'destroy'])->name('destroy');
    });
});
