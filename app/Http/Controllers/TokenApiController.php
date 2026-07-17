<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TokenApiController extends Controller
{
    /**
     * Gerencia os tokens de acesso à API do usuário autenticado.
     */
    public function index(Request $request): View
    {
        $tokens = $request->user()->tokens()->orderByDesc('created_at')->get();
        $novoToken = session('novo_token');

        return view('tokens.index', compact('tokens', 'novoToken'));
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate(['nome' => 'required|string|max:255']);

        $token = $request->user()->createToken($dados['nome']);

        return redirect()->route('tokens.index')
            ->with('novo_token', $token->plainTextToken)
            ->with('status', 'Token gerado com sucesso. Copie agora — ele não será exibido de novo.');
    }

    public function destroy(Request $request, int $tokenId): RedirectResponse
    {
        $request->user()->tokens()->where('id', $tokenId)->delete();

        return redirect()->route('tokens.index')->with('status', 'Token revogado com sucesso.');
    }
}
