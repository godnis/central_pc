<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\ItemDescarte;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemDescarteController extends Controller
{
    /**
     * Fila de peças aguardando descarte (ainda sem remessa).
     */
    public function index(): View
    {
        $itens = ItemDescarte::naFila()->orderByDesc('created_at')->get();

        return view('descarte.index', compact('itens'));
    }

    public function create(): View
    {
        return view('descarte.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'descricao' => ['required', 'string', 'max:255'],
            'diagnostico' => ['required', 'string', 'max:1000'],
        ]);

        $dados['user_id'] = $request->user()->id;

        $item = ItemDescarte::create($dados);
        Atividade::registrar($item, 'criado', "Peça \"{$item->descricao}\" adicionada à fila de descarte.");

        return redirect()->route('descarte.index')->with('status', 'Peça adicionada à fila de descarte.');
    }

    public function destroy(ItemDescarte $item): RedirectResponse
    {
        if ($item->remessa_id !== null) {
            return redirect()->route('descarte.index')
                ->with('status', 'Essa peça já faz parte de uma remessa e não pode ser removida.');
        }

        Atividade::registrar($item, 'excluido', "Peça \"{$item->descricao}\" removida da fila de descarte.");
        $item->delete();

        return redirect()->route('descarte.index')->with('status', 'Peça removida da fila.');
    }
}
