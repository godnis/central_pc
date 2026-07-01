<?php

use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\SetorController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/maquinas');

Route::middleware('auth')->group(function () {
    Route::resource('maquinas', MaquinaController::class)->except('show');
    Route::resource('setores', SetorController::class)->except('show')->parameters(['setores' => 'setor']);
});

require __DIR__.'/auth.php';
