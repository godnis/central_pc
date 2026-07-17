<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusMaquina;
use App\Http\Controllers\Concerns\GerenciaComponentesDaMaquina;
use App\Http\Controllers\Controller;
use App\Http\Resources\MaquinaResource;
use App\Models\Atividade;
use App\Models\Maquina;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaquinaApiController extends Controller
{
    use GerenciaComponentesDaMaquina;

    public function index(Request $request): JsonResponse
    {
        $maquinas = Maquina::with(['setor', 'maquinaComponentes.componente'])
            ->when($request->query('setor_id'), fn ($query) => $query->where('setor_id', $request->query('setor_id')))
            ->when($request->query('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->orderBy('nome')
            ->paginate(20);

        return MaquinaResource::collection($maquinas)->response();
    }

    public function show(Maquina $maquina): JsonResponse
    {
        $maquina->load(['setor', 'maquinaComponentes.componente']);

        return (new MaquinaResource($maquina))->response();
    }

    public function store(Request $request): JsonResponse
    {
        $dados = $this->validarDados($request);
        $this->validarCompatibilidade($dados['componentes']);

        $maquina = Maquina::create($this->dadosDaMaquina($dados));
        $this->sincronizarComponentes($maquina, $dados['componentes']);
        Atividade::registrar($maquina, 'criado', "Máquina \"{$maquina->nome}\" cadastrada via API.");

        $maquina->load(['setor', 'maquinaComponentes.componente']);

        return (new MaquinaResource($maquina))->response()->setStatusCode(201);
    }

    public function update(Request $request, Maquina $maquina): JsonResponse
    {
        $dados = $this->validarDados($request, $maquina);
        $this->validarCompatibilidade($dados['componentes']);

        $maquina->update($this->dadosDaMaquina($dados));
        $this->sincronizarComponentes($maquina, $dados['componentes']);
        Atividade::registrar($maquina, 'atualizado', "Dados de \"{$maquina->nome}\" atualizados via API.");

        $maquina->load(['setor', 'maquinaComponentes.componente']);

        return (new MaquinaResource($maquina))->response();
    }

    public function destroy(Maquina $maquina): JsonResponse
    {
        Atividade::registrar($maquina, 'excluido', "Máquina \"{$maquina->nome}\" movida para a lixeira via API.");
        $maquina->delete();

        return response()->json(null, 204);
    }

    private function validarDados(Request $request, ?Maquina $maquina = null): array
    {
        return $request->validate([
            'nome' => 'required|string|max:255',
            'patrimonio' => ['nullable', 'string', 'max:100', Rule::unique('maquinas', 'patrimonio')->ignore($maquina?->id)],
            'setor_id' => 'required|exists:setores,id',
            'status' => ['required', Rule::enum(StatusMaquina::class)],
            'sistema_operacional' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'responsavel' => 'nullable|string|max:255',
            'data_aquisicao' => 'nullable|date',
            'componentes.cpu' => 'required|integer|exists:componentes,id',
            'componentes.placa_mae' => 'required|integer|exists:componentes,id',
            'componentes.ram' => 'required|array|min:1',
            'componentes.ram.*.componente_id' => 'required|integer|exists:componentes,id',
            'componentes.ram.*.quantidade' => 'required|integer|min:1',
            'componentes.armazenamento' => 'required|array|min:1',
            'componentes.armazenamento.*.componente_id' => 'required|integer|exists:componentes,id',
            'componentes.armazenamento.*.quantidade' => 'required|integer|min:1',
            'componentes.gpu' => 'nullable|integer|exists:componentes,id',
            'componentes.fonte' => 'nullable|integer|exists:componentes,id',
            'componentes.gabinete' => 'nullable|integer|exists:componentes,id',
        ]);
    }

    private function dadosDaMaquina(array $dados): array
    {
        return [
            'nome' => $dados['nome'],
            'patrimonio' => $dados['patrimonio'] ?? null,
            'setor_id' => $dados['setor_id'],
            'status' => $dados['status'],
            'sistema_operacional' => $dados['sistema_operacional'] ?? null,
            'observacoes' => $dados['observacoes'] ?? null,
            'responsavel' => $dados['responsavel'] ?? null,
            'data_aquisicao' => $dados['data_aquisicao'] ?? null,
        ];
    }
}
