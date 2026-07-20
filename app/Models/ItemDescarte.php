<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemDescarte extends Model
{
    protected $table = 'itens_descarte';

    protected $fillable = [
        'descricao',
        'diagnostico',
        'remessa_id',
        'user_id',
    ];

    public function remessa(): BelongsTo
    {
        return $this->belongsTo(RemessaDescarte::class, 'remessa_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeNaFila(Builder $query): Builder
    {
        return $query->whereNull('remessa_id');
    }
}
