<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maquina extends Model
{
    protected $fillable = [
        'nome',
        'setor_id',
        'sistema_operacional',
        'processador',
        'memoria_ram_gb',
        'tipo_armazenamento',
        'capacidade_armazenamento_gb',
        'observacoes',
    ];

    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }
}
