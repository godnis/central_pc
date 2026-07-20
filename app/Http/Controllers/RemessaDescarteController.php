<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\ItemDescarte;
use App\Models\RemessaDescarte;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RemessaDescarteController extends Controller
{
    /**
     * Histórico de remessas já fechadas.
     */
    public function index(): View
    {
        $remessas = RemessaDescarte::withCount('itens')->orderByDesc('created_at')->get();

        return view('descarte.remessas.index', compact('remessas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'itens' => ['required', 'array', 'min:1'],
            'itens.*' => [
                'integer',
                Rule::exists('itens_descarte', 'id')->where('remessa_id', null),
            ],
        ]);

        $remessa = DB::transaction(function () use ($dados, $request) {
            $remessa = RemessaDescarte::create([
                'devolvido_por' => $request->user()->name,
                'user_id' => $request->user()->id,
            ]);

            ItemDescarte::whereIn('id', $dados['itens'])->update(['remessa_id' => $remessa->id]);

            return $remessa;
        });

        $quantidade = count($dados['itens']);
        Atividade::registrar($remessa, 'criado', "Remessa de descarte gerada com {$quantidade} peça(s).");

        return redirect()->route('descarte.remessas.show', $remessa);
    }

    /**
     * Página pronta para impressão (réplica do Anexo 7).
     */
    public function show(RemessaDescarte $remessa): View
    {
        $itens = $remessa->itens()->orderBy('id')->get();

        $paginas = $itens->chunk(20)->map(function ($chunk) {
            return $chunk->values()->pad(20, null);
        });

        if ($paginas->isEmpty()) {
            $paginas = collect([collect(array_fill(0, 20, null))]);
        }

        return view('descarte.remessas.show', compact('remessa', 'paginas'));
    }
}
