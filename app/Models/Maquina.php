<?php

namespace App\Models;

use App\Enums\CategoriaComponente;
use App\Enums\StatusMaquina;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Maquina extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nome',
        'patrimonio',
        'setor_id',
        'status',
        'sistema_operacional',
        'observacoes',
        'responsavel',
        'data_aquisicao',
        'foto_path',
    ];

    protected $casts = [
        'status' => StatusMaquina::class,
        'data_aquisicao' => 'date',
    ];

    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }

    public function maquinaComponentes(): HasMany
    {
        return $this->hasMany(MaquinaComponente::class);
    }

    public function atividades(): MorphMany
    {
        return $this->morphMany(Atividade::class, 'loggable')->latest('created_at');
    }

    public function componentesDaCategoria(CategoriaComponente $categoria): Collection
    {
        return $this->maquinaComponentes
            ->filter(fn (MaquinaComponente $vinculo) => $vinculo->componente->categoria === $categoria)
            ->pluck('componente');
    }
}
