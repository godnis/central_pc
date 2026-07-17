<?php

namespace App\Http\Controllers;

use App\Enums\CategoriaComponente;
use App\Http\Controllers\Concerns\ValidaDadosDeComponente;
use App\Models\Atividade;
use App\Models\Componente;
use App\Services\CompatibilidadeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ComponenteController extends Controller
{
    use ValidaDadosDeComponente;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $categoriaFiltro = $request->query('categoria');

        $componentes = Componente::query()
            ->when($categoriaFiltro, fn ($query) => $query->where('categoria', $categoriaFiltro))
            ->orderBy('categoria')
            ->orderBy('nome')
            ->get();

        $categorias = CategoriaComponente::cases();

        return view('componentes.index', compact('componentes', 'categorias', 'categoriaFiltro'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categorias = CategoriaComponente::cases();
        $componente = new Componente;

        return view('componentes.create', compact('categorias', 'componente'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $componente = Componente::create($this->validarDadosComponente($request));
        Atividade::registrar($componente, 'criado', "Componente \"{$componente->nome}\" cadastrado no catálogo.");

        return redirect()->route('componentes.index')->with('status', 'Componente cadastrado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Componente $componente): View
    {
        $categorias = CategoriaComponente::cases();

        return view('componentes.edit', compact('componente', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Componente $componente): RedirectResponse
    {
        $componente->update($this->validarDadosComponente($request));
        Atividade::registrar($componente, 'atualizado', "Componente \"{$componente->nome}\" atualizado.");

        return redirect()->route('componentes.index')->with('status', 'Componente atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Componente $componente): RedirectResponse
    {
        Atividade::registrar($componente, 'excluido', "Componente \"{$componente->nome}\" excluído do catálogo.");
        $componente->delete();

        return redirect()->route('componentes.index')->with('status', 'Componente excluído com sucesso.');
    }

    /**
     * Retorna, em JSON, os componentes ativos de uma categoria compatíveis
     * com os componentes já selecionados no formulário de máquina.
     */
    public function compativeis(Request $request, CompatibilidadeService $service): JsonResponse
    {
        $validado = $request->validate([
            'categoria_alvo' => ['required', Rule::enum(CategoriaComponente::class)],
            'selecionados' => ['sometimes', 'array'],
            'selecionados.*' => ['array'],
            'selecionados.*.*' => ['integer'],
        ]);

        $categoriaAlvo = CategoriaComponente::from($validado['categoria_alvo']);

        $selecionados = [];
        foreach ($validado['selecionados'] ?? [] as $categoriaValue => $ids) {
            $selecionados[$categoriaValue] = Componente::query()->whereIn('id', $ids)->get();
        }

        $componentes = $service->componentesCompativeis($selecionados, $categoriaAlvo);
        $avisoFonte = $service->avisoFontePotencia($selecionados);

        return response()->json([
            'componentes' => $componentes->map(fn (Componente $c) => [
                'id' => $c->id,
                'nome' => $c->nome,
                'fabricante' => $c->fabricante,
            ]),
            'aviso_fonte' => $avisoFonte,
        ]);
    }
}
