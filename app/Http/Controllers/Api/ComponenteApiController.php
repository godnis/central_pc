<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ValidaDadosDeComponente;
use App\Http\Controllers\Controller;
use App\Http\Resources\ComponenteResource;
use App\Models\Atividade;
use App\Models\Componente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComponenteApiController extends Controller
{
    use ValidaDadosDeComponente;

    public function index(Request $request): JsonResponse
    {
        $componentes = Componente::query()
            ->when($request->query('categoria'), fn ($query) => $query->where('categoria', $request->query('categoria')))
            ->when($request->boolean('apenas_ativos'), fn ($query) => $query->ativos())
            ->orderBy('categoria')->orderBy('nome')
            ->get();

        return ComponenteResource::collection($componentes)->response();
    }

    public function show(Componente $componente): JsonResponse
    {
        return (new ComponenteResource($componente))->response();
    }

    public function store(Request $request): JsonResponse
    {
        $componente = Componente::create($this->validarDadosComponente($request));
        Atividade::registrar($componente, 'criado', "Componente \"{$componente->nome}\" cadastrado via API.");

        return (new ComponenteResource($componente))->response()->setStatusCode(201);
    }

    public function update(Request $request, Componente $componente): JsonResponse
    {
        $componente->update($this->validarDadosComponente($request));
        Atividade::registrar($componente, 'atualizado', "Componente \"{$componente->nome}\" atualizado via API.");

        return (new ComponenteResource($componente))->response();
    }

    public function destroy(Componente $componente): JsonResponse
    {
        Atividade::registrar($componente, 'excluido', "Componente \"{$componente->nome}\" excluído via API.");
        $componente->delete();

        return response()->json(null, 204);
    }
}
