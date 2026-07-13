<?php

namespace App\Enums;

enum CategoriaComponente: string
{
    case Cpu = 'cpu';
    case PlacaMae = 'placa_mae';
    case Ram = 'ram';
    case Armazenamento = 'armazenamento';
    case Gpu = 'gpu';
    case Fonte = 'fonte';
    case Gabinete = 'gabinete';

    public function label(): string
    {
        return match ($this) {
            self::Cpu => 'Processador',
            self::PlacaMae => 'Placa-mãe',
            self::Ram => 'Memória RAM',
            self::Armazenamento => 'Armazenamento',
            self::Gpu => 'Placa de vídeo',
            self::Fonte => 'Fonte',
            self::Gabinete => 'Gabinete',
        };
    }

    /**
     * Categorias exigidas no cadastro de uma máquina.
     *
     * @return CategoriaComponente[]
     */
    public static function obrigatorias(): array
    {
        return [self::Cpu, self::PlacaMae, self::Ram, self::Armazenamento];
    }

    /**
     * Categorias que podem ter mais de um item vinculado à mesma máquina.
     *
     * @return CategoriaComponente[]
     */
    public static function permitemMultiplos(): array
    {
        return [self::Ram, self::Armazenamento];
    }

    /**
     * Campos técnicos esperados em `specs` para esta categoria, com o tipo
     * de valor (`string`, `integer` ou `array` para campos de múltipla
     * escolha). Usado tanto para montar o formulário do catálogo quanto
     * para extrair/validar os dados enviados.
     *
     * @return array<string, 'string'|'integer'|'array'>
     */
    public function camposDeSpecs(): array
    {
        return match ($this) {
            self::Cpu => [
                'socket' => 'string',
                'tdp_watts' => 'integer',
            ],
            self::PlacaMae => [
                'socket' => 'string',
                'form_factor' => 'string',
                'tipos_ram_suportados' => 'array',
                'interfaces_armazenamento_suportadas' => 'array',
                'slots_ram' => 'integer',
            ],
            self::Ram => [
                'tipo' => 'string',
                'capacidade_gb' => 'integer',
                'velocidade_mhz' => 'integer',
            ],
            self::Armazenamento => [
                'tipo' => 'string',
                'interface' => 'string',
                'capacidade_gb' => 'integer',
            ],
            self::Gpu => [
                'consumo_watts' => 'integer',
            ],
            self::Fonte => [
                'potencia_watts' => 'integer',
            ],
            self::Gabinete => [
                'form_factors_suportados' => 'array',
            ],
        };
    }
}
