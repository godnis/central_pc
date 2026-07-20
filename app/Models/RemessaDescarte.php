<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RemessaDescarte extends Model
{
    protected $table = 'remessas_descarte';

    protected $fillable = [
        'devolvido_por',
        'user_id',
    ];

    public function itens(): HasMany
    {
        return $this->hasMany(ItemDescarte::class, 'remessa_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
