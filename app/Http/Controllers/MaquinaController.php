<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use App\Models\Setor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaquinaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $setorId = $request->query('setor_id');

        $maquinas = Maquina::with('setor')
            ->when($setorId, fn ($query) => $query->where('setor_id', $setorId))
            ->orderBy('nome')
            ->get();

        $totalGeral = Maquina::count();

        $totalPorSetor = Setor::withCount('maquinas')
            ->get()
            ->filter(fn ($setor) => $setor->maquinas_count > 0)
            ->sortByDesc('maquinas_count');

        $setores = Setor::orderBy('nome')->get();

        return view('maquinas.index', compact('maquinas', 'totalGeral', 'totalPorSetor', 'setores', 'setorId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $setores = Setor::orderBy('nome')->get();

        return view('maquinas.create', compact('setores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $dados = $this->validarDados($request);

        Maquina::create($dados);

        return redirect()->route('maquinas.index')->with('status', 'Máquina cadastrada com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maquina $maquina): View
    {
        $setores = Setor::orderBy('nome')->get();

        return view('maquinas.edit', compact('maquina', 'setores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Maquina $maquina): RedirectResponse
    {
        $dados = $this->validarDados($request);

        $maquina->update($dados);

        return redirect()->route('maquinas.index')->with('status', 'Máquina atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Maquina $maquina): RedirectResponse
    {
        $maquina->delete();

        return redirect()->route('maquinas.index')->with('status', 'Máquina excluída com sucesso.');
    }

    /**
     * Valida os dados do formulário de máquina.
     */
    private function validarDados(Request $request): array
    {
        return $request->validate([
            'nome' => 'required|string|max:255',
            'setor_id' => 'required|exists:setores,id',
            'sistema_operacional' => 'nullable|string|max:255',
            'processador' => 'required|string|max:255',
            'memoria_ram_gb' => 'nullable|integer|min:1',
            'tipo_armazenamento' => 'required|in:HD,SSD',
            'capacidade_armazenamento_gb' => 'required|integer|min:1',
            'observacoes' => 'nullable|string',
        ]);
    }
}
