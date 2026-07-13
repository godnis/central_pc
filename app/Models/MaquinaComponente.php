<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaquinaComponente extends Model
{
    protected $fillable = [
        'maquina_id',
        'componente_id',
        'quantidade',
    ];

    protected $casts = [
        'quantidade' => 'integer',
    ];

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquina::class);
    }

    public function componente(): BelongsTo
    {
        return $this->belongsTo(Componente::class);
    }
}
