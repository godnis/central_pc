<?php

namespace App\Services;

use App\Enums\CategoriaComponente;
use App\Models\Componente;
use Illuminate\Support\Collection;

/**
 * Motor de compatibilidade entre componentes de hardware.
 *
 * Regras conhecidas (ver spec em docs/superpowers/specs):
 * 1. CPU ↔ Placa-mãe: mesmo socket
 * 2. Placa-mãe ↔ RAM: tipo de RAM suportado pela placa
 * 3. Placa-mãe ↔ Armazenamento: interface suportada pela placa
 * 4. Placa-mãe ↔ Gabinete: form factor da placa suportado pelo gabinete
 * 5. Fonte ↔ CPU+GPU: aviso (não bloqueia) se a potência parecer insuficiente
 */
class CompatibilidadeService
{
    private const MARGEM_FONTE_WATTS = 100;

    /**
     * Retorna os componentes ativos de $categoriaAlvo compatíveis com o que
     * já foi selecionado em $selecionados. Regras que não se aplicam à
     * combinação de categorias envolvidas simplesmente não filtram nada.
     *
     * @param  array<string, Componente|Collection<int, Componente>|null>  $selecionados  mapa categoria->value() => componente(s) já escolhidos
     */
    public function componentesCompativeis(array $selecionados, CategoriaComponente $categoriaAlvo): Collection
    {
        return Componente::query()
            ->ativos()
            ->daCategoria($categoriaAlvo)
            ->get()
            ->filter(fn (Componente $candidato) => $this->compativel($candidato, $categoriaAlvo, $selecionados))
            ->values();
    }

    /**
     * Mensagem de aviso (não bloqueante) se a fonte selecionada parecer
     * insuficiente para o CPU + GPU(s) selecionados. Retorna null se não há
     * fonte selecionada ou se a potência é suficiente.
     *
     * @param  array<string, Componente|Collection<int, Componente>|null>  $selecionados
     */
    public function avisoFontePotencia(array $selecionados): ?string
    {
        $fonte = $this->componenteUnico($selecionados, CategoriaComponente::Fonte);

        if (! $fonte) {
            return null;
        }

        $cpu = $this->componenteUnico($selecionados, CategoriaComponente::Cpu);
        $gpus = $this->componentesDaSelecao($selecionados, CategoriaComponente::Gpu);

        $tdpCpu = (int) ($cpu?->specs['tdp_watts'] ?? 0);
        $consumoGpu = $gpus->sum(fn (Componente $gpu) => (int) ($gpu->specs['consumo_watts'] ?? 0));
        $necessario = $tdpCpu + $consumoGpu + self::MARGEM_FONTE_WATTS;
        $potenciaFonte = (int) ($fonte->specs['potencia_watts'] ?? 0);

        if ($potenciaFonte < $necessario) {
            return "A fonte selecionada ({$potenciaFonte}W) pode ser insuficiente. Estimativa mínima recomendada: {$necessario}W.";
        }

        return null;
    }

    private function compativel(Componente $candidato, CategoriaComponente $categoriaAlvo, array $selecionados): bool
    {
        return match ($categoriaAlvo) {
            CategoriaComponente::Cpu => $this->cpuCompativel($candidato, $selecionados),
            CategoriaComponente::PlacaMae => $this->placaMaeCompativel($candidato, $selecionados),
            CategoriaComponente::Ram => $this->ramCompativel($candidato, $selecionados),
            CategoriaComponente::Armazenamento => $this->armazenamentoCompativel($candidato, $selecionados),
            CategoriaComponente::Gabinete => $this->gabineteCompativel($candidato, $selecionados),
            default => true,
        };
    }

    private function cpuCompativel(Componente $candidato, array $selecionados): bool
    {
        $placaMae = $this->componenteUnico($selecionados, CategoriaComponente::PlacaMae);

        if (! $placaMae) {
            return true;
        }

        return ($candidato->specs['socket'] ?? null) === ($placaMae->specs['socket'] ?? null);
    }

    private function placaMaeCompativel(Componente $candidato, array $selecionados): bool
    {
        $cpu = $this->componenteUnico($selecionados, CategoriaComponente::Cpu);
        if ($cpu && ($candidato->specs['socket'] ?? null) !== ($cpu->specs['socket'] ?? null)) {
            return false;
        }

        $tiposRamSuportados = $candidato->specs['tipos_ram_suportados'] ?? [];
        foreach ($this->componentesDaSelecao($selecionados, CategoriaComponente::Ram) as $ram) {
            if (! in_array($ram->specs['tipo'] ?? null, $tiposRamSuportados, true)) {
                return false;
            }
        }

        $interfacesSuportadas = $candidato->specs['interfaces_armazenamento_suportadas'] ?? [];
        foreach ($this->componentesDaSelecao($selecionados, CategoriaComponente::Armazenamento) as $armazenamento) {
            if (! in_array($armazenamento->specs['interface'] ?? null, $interfacesSuportadas, true)) {
                return false;
            }
        }

        $gabinete = $this->componenteUnico($selecionados, CategoriaComponente::Gabinete);
        if ($gabinete) {
            $formFactorsDoGabinete = $gabinete->specs['form_factors_suportados'] ?? [];
            if (! in_array($candidato->specs['form_factor'] ?? null, $formFactorsDoGabinete, true)) {
                return false;
            }
        }

        return true;
    }

    private function ramCompativel(Componente $candidato, array $selecionados): bool
    {
        $placaMae = $this->componenteUnico($selecionados, CategoriaComponente::PlacaMae);

        if (! $placaMae) {
            return true;
        }

        $tiposSuportados = $placaMae->specs['tipos_ram_suportados'] ?? [];

        return in_array($candidato->specs['tipo'] ?? null, $tiposSuportados, true);
    }

    private function armazenamentoCompativel(Componente $candidato, array $selecionados): bool
    {
        $placaMae = $this->componenteUnico($selecionados, CategoriaComponente::PlacaMae);

        if (! $placaMae) {
            return true;
        }

        $interfacesSuportadas = $placaMae->specs['interfaces_armazenamento_suportadas'] ?? [];

        return in_array($candidato->specs['interface'] ?? null, $interfacesSuportadas, true);
    }

    private function gabineteCompativel(Componente $candidato, array $selecionados): bool
    {
        $placaMae = $this->componenteUnico($selecionados, CategoriaComponente::PlacaMae);

        if (! $placaMae) {
            return true;
        }

        $formFactorsSuportados = $candidato->specs['form_factors_suportados'] ?? [];

        return in_array($placaMae->specs['form_factor'] ?? null, $formFactorsSuportados, true);
    }

    private function componenteUnico(array $selecionados, CategoriaComponente $categoria): ?Componente
    {
        return $this->componentesDaSelecao($selecionados, $categoria)->first();
    }

    /**
     * @return Collection<int, Componente>
     */
    private function componentesDaSelecao(array $selecionados, CategoriaComponente $categoria): Collection
    {
        $valor = $selecionados[$categoria->value] ?? null;

        return match (true) {
            $valor instanceof Componente => collect([$valor]),
            $valor instanceof Collection => $valor,
            is_array($valor) => collect($valor),
            default => collect(),
        };
    }
}
