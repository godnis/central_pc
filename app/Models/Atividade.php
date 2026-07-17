<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Atividade extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'loggable_type',
        'loggable_id',
        'acao',
        'descricao',
    ];

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Registra uma entrada de auditoria para o model informado.
     */
    public static function registrar(Model $model, string $acao, ?string $descricao = null): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'loggable_type' => $model::class,
            'loggable_id' => $model->getKey(),
            'acao' => $acao,
            'descricao' => $descricao,
        ]);
    }
}
