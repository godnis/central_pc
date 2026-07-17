<?php

namespace App\Http\Controllers\Concerns;

use App\Enums\CategoriaComponente;
use App\Models\Componente;
use App\Models\Maquina;
use App\Services\CompatibilidadeService;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Lógica de validação de compatibilidade e persistência dos componentes de
 * uma máquina, compartilhada entre o controller web e o da API.
 */
trait GerenciaComponentesDaMaquina
{
    /**
     * Reaplica as regras de compatibilidade no backend (o formulário/cliente
     * já restringe as opções via AJAX, mas isso não deve ser a única defesa).
     */
    protected function validarCompatibilidade(array $componentes): void
    {
        $ids = collect([
            $componentes['cpu'] ?? null,
            $componentes['placa_mae'] ?? null,
            $componentes['gpu'] ?? null,
            $componentes['fonte'] ?? null,
            $componentes['gabinete'] ?? null,
        ])
            ->merge(collect($componentes['ram'] ?? [])->pluck('componente_id'))
            ->merge(collect($componentes['armazenamento'] ?? [])->pluck('componente_id'))
            ->filter()
            ->unique();

        $porId = Componente::query()->whereIn('id', $ids)->get()->keyBy('id');
        $buscar = fn (?int $id) => $id ? $porId->get($id) : null;

        $selecionados = [
            CategoriaComponente::Cpu->value => $buscar($componentes['cpu'] ?? null),
            CategoriaComponente::PlacaMae->value => $buscar($componentes['placa_mae'] ?? null),
            CategoriaComponente::Ram->value => collect($componentes['ram'] ?? [])
                ->map(fn ($item) => $buscar($item['componente_id']))->filter()->values(),
            CategoriaComponente::Armazenamento->value => collect($componentes['armazenamento'] ?? [])
                ->map(fn ($item) => $buscar($item['componente_id']))->filter()->values(),
            CategoriaComponente::Gpu->value => $buscar($componentes['gpu'] ?? null),
            CategoriaComponente::Fonte->value => $buscar($componentes['fonte'] ?? null),
            CategoriaComponente::Gabinete->value => $buscar($componentes['gabinete'] ?? null),
        ];

        $service = app(CompatibilidadeService::class);
        $erros = [];

        foreach ([CategoriaComponente::Cpu, CategoriaComponente::PlacaMae, CategoriaComponente::Ram, CategoriaComponente::Armazenamento, CategoriaComponente::Gabinete] as $categoria) {
            $valor = $selecionados[$categoria->value];
            $itens = $valor instanceof Collection ? $valor : collect($valor ? [$valor] : []);

            if ($itens->isEmpty()) {
                continue;
            }

            // Array puro (sem passar por Collection::toArray(), que chamaria
            // Componente::toArray() e transformaria os models em arrays).
            $outrasSelecoes = $selecionados;
            unset($outrasSelecoes[$categoria->value]);
            $idsCompativeis = $service->componentesCompativeis($outrasSelecoes, $categoria)->pluck('id');

            foreach ($itens as $item) {
                if (! $idsCompativeis->contains($item->id)) {
                    $erros["componentes.{$categoria->value}"] = 'O componente selecionado para '
                        .$categoria->label().' não é compatível com os demais componentes escolhidos.';
                    break;
                }
            }
        }

        if ($erros) {
            throw ValidationException::withMessages($erros);
        }
    }

    protected function sincronizarComponentes(Maquina $maquina, array $componentes): void
    {
        $maquina->maquinaComponentes()->delete();

        $linhas = [];

        foreach ([CategoriaComponente::Cpu, CategoriaComponente::PlacaMae, CategoriaComponente::Gpu, CategoriaComponente::Fonte, CategoriaComponente::Gabinete] as $categoria) {
            $id = $componentes[$categoria->value] ?? null;
            if ($id) {
                $linhas[] = ['componente_id' => $id, 'quantidade' => 1];
            }
        }

        foreach ([CategoriaComponente::Ram, CategoriaComponente::Armazenamento] as $categoria) {
            foreach ($componentes[$categoria->value] ?? [] as $item) {
                $linhas[] = ['componente_id' => $item['componente_id'], 'quantidade' => $item['quantidade']];
            }
        }

        foreach ($linhas as $linha) {
            $maquina->maquinaComponentes()->create($linha);
        }
    }
}
