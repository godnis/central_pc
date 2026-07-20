<?php

use App\Http\Controllers\ComponenteController;
use App\Http\Controllers\ItemDescarteController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\RemessaDescarteController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\TokenApiController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/maquinas');

Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::get('maquinas/lixeira', [MaquinaController::class, 'lixeira'])->middleware('can:excluir')->name('maquinas.lixeira');
    Route::post('maquinas/{id}/restaurar', [MaquinaController::class, 'restaurar'])->middleware('can:excluir')->name('maquinas.restaurar');
    Route::delete('maquinas/{id}/excluir-definitivamente', [MaquinaController::class, 'excluirDefinitivamente'])->middleware('can:excluir')->name('maquinas.excluirDefinitivamente');
    Route::get('maquinas/exportar', [MaquinaController::class, 'export'])->name('maquinas.export');
    Route::get('maquinas/{maquina}/qrcode', [MaquinaController::class, 'qrcode'])->name('maquinas.qrcode');
    Route::resource('maquinas', MaquinaController::class)
        ->middlewareFor(['create', 'store', 'edit', 'update'], 'can:editar')
        ->middlewareFor('destroy', 'can:excluir');

    Route::resource('setores', SetorController::class)->except('show')->parameters(['setores' => 'setor'])
        ->middlewareFor(['create', 'store', 'edit', 'update'], 'can:editar')
        ->middlewareFor('destroy', 'can:excluir');

    Route::post('componentes/compativeis', [ComponenteController::class, 'compativeis'])->name('componentes.compativeis');
    Route::resource('componentes', ComponenteController::class)->except('show')
        ->middlewareFor(['create', 'store', 'edit', 'update'], 'can:editar')
        ->middlewareFor('destroy', 'can:excluir');

    Route::get('descarte/remessas', [RemessaDescarteController::class, 'index'])->name('descarte.remessas.index');
    Route::post('descarte/remessas', [RemessaDescarteController::class, 'store'])->middleware('can:editar')->name('descarte.remessas.store');
    Route::get('descarte/remessas/{remessa}', [RemessaDescarteController::class, 'show'])->name('descarte.remessas.show');
    Route::get('descarte/criar', [ItemDescarteController::class, 'create'])->middleware('can:editar')->name('descarte.create');
    Route::delete('descarte/{item}', [ItemDescarteController::class, 'destroy'])->middleware('can:editar')->name('descarte.destroy');
    Route::get('descarte', [ItemDescarteController::class, 'index'])->name('descarte.index');
    Route::post('descarte', [ItemDescarteController::class, 'store'])->middleware('can:editar')->name('descarte.store');

    Route::middleware('can:excluir')->group(function () {
        Route::get('tokens', [TokenApiController::class, 'index'])->name('tokens.index');
        Route::post('tokens', [TokenApiController::class, 'store'])->name('tokens.store');
        Route::delete('tokens/{tokenId}', [TokenApiController::class, 'destroy'])->name('tokens.destroy');
    });
});

require __DIR__.'/auth.php';
