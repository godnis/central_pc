<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setor extends Model
{
    protected $table = 'setores';

    protected $fillable = ['nome'];

    public function maquinas(): HasMany
    {
        return $this->hasMany(Maquina::class);
    }
}
