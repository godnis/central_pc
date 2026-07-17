<?php

namespace App\Http\Resources;

use App\Enums\CategoriaComponente;
use App\Models\Maquina;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Maquina */
class MaquinaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'patrimonio' => $this->patrimonio,
            'setor' => new SetorResource($this->whenLoaded('setor')),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'sistema_operacional' => $this->sistema_operacional,
            'responsavel' => $this->responsavel,
            'data_aquisicao' => optional($this->data_aquisicao)->toDateString(),
            'observacoes' => $this->observacoes,
            'componentes' => [
                'cpu' => ComponenteResource::collection($this->componentesDaCategoria(CategoriaComponente::Cpu)),
                'placa_mae' => ComponenteResource::collection($this->componentesDaCategoria(CategoriaComponente::PlacaMae)),
                'ram' => ComponenteResource::collection($this->componentesDaCategoria(CategoriaComponente::Ram)),
                'armazenamento' => ComponenteResource::collection($this->componentesDaCategoria(CategoriaComponente::Armazenamento)),
                'gpu' => ComponenteResource::collection($this->componentesDaCategoria(CategoriaComponente::Gpu)),
                'fonte' => ComponenteResource::collection($this->componentesDaCategoria(CategoriaComponente::Fonte)),
                'gabinete' => ComponenteResource::collection($this->componentesDaCategoria(CategoriaComponente::Gabinete)),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
