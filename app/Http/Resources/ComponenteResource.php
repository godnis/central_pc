<?php

namespace App\Http\Resources;

use App\Models\Componente;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Componente */
class ComponenteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'categoria' => $this->categoria->value,
            'categoria_label' => $this->categoria->label(),
            'nome' => $this->nome,
            'fabricante' => $this->fabricante,
            'specs' => $this->specs,
            'ativo' => $this->ativo,
        ];
    }
}
