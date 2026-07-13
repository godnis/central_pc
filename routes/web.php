<?php

use App\Http\Controllers\ComponenteController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\SetorController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/maquinas');

Route::middleware('auth')->group(function () {
    Route::resource('maquinas', MaquinaController::class)->except('show');
    Route::resource('setores', SetorController::class)->except('show')->parameters(['setores' => 'setor']);

    Route::post('componentes/compativeis', [ComponenteController::class, 'compativeis'])->name('componentes.compativeis');
    Route::resource('componentes', ComponenteController::class)->except('show');
});

require __DIR__.'/auth.php';
