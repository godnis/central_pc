<?php

namespace App\Models;

use App\Enums\CategoriaComponente;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Maquina extends Model
{
    protected $fillable = [
        'nome',
        'setor_id',
        'sistema_operacional',
        'observacoes',
    ];

    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }

    public function maquinaComponentes(): HasMany
    {
        return $this->hasMany(MaquinaComponente::class);
    }

    public function componentesDaCategoria(CategoriaComponente $categoria): Collection
    {
        return $this->maquinaComponentes
            ->filter(fn (MaquinaComponente $vinculo) => $vinculo->componente->categoria === $categoria)
            ->pluck('componente');
    }
}
