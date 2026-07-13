<?php

namespace App\Models;

use App\Enums\CategoriaComponente;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Componente extends Model
{
    protected $fillable = [
        'categoria',
        'nome',
        'fabricante',
        'specs',
        'ativo',
    ];

    protected $casts = [
        'categoria' => CategoriaComponente::class,
        'specs' => 'array',
        'ativo' => 'boolean',
    ];

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    public function scopeDaCategoria(Builder $query, CategoriaComponente $categoria): Builder
    {
        return $query->where('categoria', $categoria);
    }
}
