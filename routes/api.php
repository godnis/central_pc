<?php

use App\Http\Controllers\Api\ComponenteApiController;
use App\Http\Controllers\Api\MaquinaApiController;
use App\Http\Controllers\Api\SetorApiController;
use Illuminate\Support\Facades\Route;

// Nomes prefixados com "api." — sem isso, colidem com as rotas web de mesmo
// nome (ex: "maquinas.index") e quebram os route() usados nas views.
Route::name('api.')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::apiResource('maquinas', MaquinaApiController::class)
        ->middlewareFor(['store', 'update'], 'can:editar')
        ->middlewareFor('destroy', 'can:excluir');

    Route::apiResource('setores', SetorApiController::class)
        ->middlewareFor(['store', 'update'], 'can:editar')
        ->middlewareFor('destroy', 'can:excluir');

    Route::apiResource('componentes', ComponenteApiController::class)
        ->middlewareFor(['store', 'update'], 'can:editar')
        ->middlewareFor('destroy', 'can:excluir');
});
