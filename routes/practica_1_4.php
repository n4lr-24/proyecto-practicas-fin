<?php

use App\Http\Controllers\Practica1_4\ArticuloController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('catalogo-articulos')->name('articulos.')->group(function () {
    Route::get('/', [ArticuloController::class, 'index'])
        ->middleware('rol:administrador,empleado')
        ->name('index');

    Route::middleware('rol:administrador')->group(function () {
        Route::get('/nuevo', [ArticuloController::class, 'create'])->name('create');
        Route::post('/', [ArticuloController::class, 'store'])->name('store');
        Route::get('/{articulo}/editar', [ArticuloController::class, 'edit'])->name('edit');
        Route::put('/{articulo}', [ArticuloController::class, 'update'])->name('update');
        Route::delete('/{articulo}', [ArticuloController::class, 'destroy'])->name('destroy');
    });
});
