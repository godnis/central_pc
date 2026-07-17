<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\Setor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $setores = Setor::orderBy('nome')->get();

        return view('setores.index', compact('setores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('setores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $setor = Setor::create($dados);
        Atividade::registrar($setor, 'criado', "Setor \"{$setor->nome}\" criado.");

        return redirect()->route('setores.index')->with('status', 'Setor criado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setor $setor): View
    {
        return view('setores.edit', compact('setor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setor $setor): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $setor->update($dados);
        Atividade::registrar($setor, 'atualizado', "Setor atualizado para \"{$setor->nome}\".");

        return redirect()->route('setores.index')->with('status', 'Setor atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setor $setor): RedirectResponse
    {
        if ($setor->maquinas()->exists()) {
            return redirect()->route('setores.index')
                ->with('status', 'Não é possível excluir um setor que ainda tem máquinas cadastradas.');
        }

        Atividade::registrar($setor, 'excluido', "Setor \"{$setor->nome}\" excluído.");
        $setor->delete();

        return redirect()->route('setores.index')->with('status', 'Setor excluído com sucesso.');
    }
}
