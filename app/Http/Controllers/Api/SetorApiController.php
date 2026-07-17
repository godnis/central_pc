<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SetorResource;
use App\Models\Atividade;
use App\Models\Setor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetorApiController extends Controller
{
    public function index(): JsonResponse
    {
        return SetorResource::collection(Setor::orderBy('nome')->get())->response();
    }

    public function show(Setor $setor): JsonResponse
    {
        return (new SetorResource($setor))->response();
    }

    public function store(Request $request): JsonResponse
    {
        $dados = $request->validate(['nome' => 'required|string|max:255']);

        $setor = Setor::create($dados);
        Atividade::registrar($setor, 'criado', "Setor \"{$setor->nome}\" criado via API.");

        return (new SetorResource($setor))->response()->setStatusCode(201);
    }

    public function update(Request $request, Setor $setor): JsonResponse
    {
        $dados = $request->validate(['nome' => 'required|string|max:255']);

        $setor->update($dados);
        Atividade::registrar($setor, 'atualizado', "Setor atualizado para \"{$setor->nome}\" via API.");

        return (new SetorResource($setor))->response();
    }

    public function destroy(Setor $setor): JsonResponse
    {
        if ($setor->maquinas()->exists()) {
            return response()->json([
                'message' => 'Não é possível excluir um setor que ainda tem máquinas cadastradas.',
            ], 422);
        }

        Atividade::registrar($setor, 'excluido', "Setor \"{$setor->nome}\" excluído via API.");
        $setor->delete();

        return response()->json(null, 204);
    }
}
