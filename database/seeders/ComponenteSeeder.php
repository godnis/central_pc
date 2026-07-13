<?php

namespace Database\Seeders;

use App\Enums\CategoriaComponente;
use App\Models\Componente;
use Illuminate\Database\Seeder;

class ComponenteSeeder extends Seeder
{
    /**
     * Catálogo de exemplo com specs completas, para demonstrar o motor de
     * compatibilidade (ver App\Services\CompatibilidadeService).
     */
    public function run(): void
    {
        $componentes = [
            // CPU
            ['categoria' => CategoriaComponente::Cpu, 'nome' => 'Intel Core i5-10400', 'fabricante' => 'Intel',
                'specs' => ['socket' => 'LGA1200', 'tdp_watts' => 65]],
            ['categoria' => CategoriaComponente::Cpu, 'nome' => 'Intel Core i7-12700', 'fabricante' => 'Intel',
                'specs' => ['socket' => 'LGA1700', 'tdp_watts' => 65]],
            ['categoria' => CategoriaComponente::Cpu, 'nome' => 'AMD Ryzen 5 5600', 'fabricante' => 'AMD',
                'specs' => ['socket' => 'AM4', 'tdp_watts' => 65]],

            // Placa-mãe
            ['categoria' => CategoriaComponente::PlacaMae, 'nome' => 'ASUS Prime H510M-E', 'fabricante' => 'ASUS',
                'specs' => ['socket' => 'LGA1200', 'form_factor' => 'Micro-ATX', 'tipos_ram_suportados' => ['DDR4'],
                    'interfaces_armazenamento_suportadas' => ['SATA', 'NVMe'], 'slots_ram' => 2]],
            ['categoria' => CategoriaComponente::PlacaMae, 'nome' => 'ASUS Prime Z690-P', 'fabricante' => 'ASUS',
                'specs' => ['socket' => 'LGA1700', 'form_factor' => 'ATX', 'tipos_ram_suportados' => ['DDR5'],
                    'interfaces_armazenamento_suportadas' => ['SATA', 'NVMe'], 'slots_ram' => 4]],
            ['categoria' => CategoriaComponente::PlacaMae, 'nome' => 'Gigabyte B550M DS3H', 'fabricante' => 'Gigabyte',
                'specs' => ['socket' => 'AM4', 'form_factor' => 'Micro-ATX', 'tipos_ram_suportados' => ['DDR4'],
                    'interfaces_armazenamento_suportadas' => ['SATA', 'NVMe'], 'slots_ram' => 4]],

            // RAM
            ['categoria' => CategoriaComponente::Ram, 'nome' => 'Kingston 8GB DDR4 2666MHz', 'fabricante' => 'Kingston',
                'specs' => ['tipo' => 'DDR4', 'capacidade_gb' => 8, 'velocidade_mhz' => 2666]],
            ['categoria' => CategoriaComponente::Ram, 'nome' => 'Kingston 16GB DDR4 3200MHz', 'fabricante' => 'Kingston',
                'specs' => ['tipo' => 'DDR4', 'capacidade_gb' => 16, 'velocidade_mhz' => 3200]],
            ['categoria' => CategoriaComponente::Ram, 'nome' => 'Corsair 16GB DDR5 5200MHz', 'fabricante' => 'Corsair',
                'specs' => ['tipo' => 'DDR5', 'capacidade_gb' => 16, 'velocidade_mhz' => 5200]],

            // Armazenamento
            ['categoria' => CategoriaComponente::Armazenamento, 'nome' => 'Kingston SSD 240GB SATA', 'fabricante' => 'Kingston',
                'specs' => ['tipo' => 'SSD', 'interface' => 'SATA', 'capacidade_gb' => 240]],
            ['categoria' => CategoriaComponente::Armazenamento, 'nome' => 'WD Blue SSD 480GB NVMe', 'fabricante' => 'Western Digital',
                'specs' => ['tipo' => 'SSD', 'interface' => 'NVMe', 'capacidade_gb' => 480]],
            ['categoria' => CategoriaComponente::Armazenamento, 'nome' => 'Seagate HD 1TB SATA', 'fabricante' => 'Seagate',
                'specs' => ['tipo' => 'HD', 'interface' => 'SATA', 'capacidade_gb' => 1000]],

            // GPU
            ['categoria' => CategoriaComponente::Gpu, 'nome' => 'NVIDIA GTX 1050 Ti', 'fabricante' => 'NVIDIA',
                'specs' => ['consumo_watts' => 75]],
            ['categoria' => CategoriaComponente::Gpu, 'nome' => 'NVIDIA RTX 3060', 'fabricante' => 'NVIDIA',
                'specs' => ['consumo_watts' => 170]],

            // Fonte
            ['categoria' => CategoriaComponente::Fonte, 'nome' => 'Corsair VS500', 'fabricante' => 'Corsair',
                'specs' => ['potencia_watts' => 500]],
            ['categoria' => CategoriaComponente::Fonte, 'nome' => 'Corsair CV650', 'fabricante' => 'Corsair',
                'specs' => ['potencia_watts' => 650]],

            // Gabinete
            ['categoria' => CategoriaComponente::Gabinete, 'nome' => 'Gabinete ATX Padrão', 'fabricante' => null,
                'specs' => ['form_factors_suportados' => ['ATX', 'Micro-ATX', 'Mini-ITX']]],
            ['categoria' => CategoriaComponente::Gabinete, 'nome' => 'Gabinete Micro Compacto', 'fabricante' => null,
                'specs' => ['form_factors_suportados' => ['Micro-ATX', 'Mini-ITX']]],
        ];

        foreach ($componentes as $componente) {
            Componente::create([
                'categoria' => $componente['categoria'],
                'nome' => $componente['nome'],
                'fabricante' => $componente['fabricante'],
                'specs' => $componente['specs'],
                'ativo' => true,
            ]);
        }
    }
}
