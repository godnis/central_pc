<?php

namespace Tests\Unit;

use App\Enums\CategoriaComponente;
use App\Models\Componente;
use App\Services\CompatibilidadeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompatibilidadeServiceTest extends TestCase
{
    use RefreshDatabase;

    private CompatibilidadeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CompatibilidadeService;
    }

    private function componente(CategoriaComponente $categoria, array $specs, string $nome = 'Componente'): Componente
    {
        return Componente::create([
            'categoria' => $categoria,
            'nome' => $nome,
            'specs' => $specs,
            'ativo' => true,
        ]);
    }

    public function test_placa_mae_compativel_filtra_por_socket_da_cpu(): void
    {
        $cpuLga1200 = $this->componente(CategoriaComponente::Cpu, ['socket' => 'LGA1200'], 'CPU LGA1200');
        $placaLga1200 = $this->componente(CategoriaComponente::PlacaMae, ['socket' => 'LGA1200', 'form_factor' => 'ATX', 'tipos_ram_suportados' => ['DDR4'], 'interfaces_armazenamento_suportadas' => ['SATA']], 'Placa LGA1200');
        $placaAm4 = $this->componente(CategoriaComponente::PlacaMae, ['socket' => 'AM4', 'form_factor' => 'ATX', 'tipos_ram_suportados' => ['DDR4'], 'interfaces_armazenamento_suportadas' => ['SATA']], 'Placa AM4');

        $compativeis = $this->service->componentesCompativeis(
            [CategoriaComponente::Cpu->value => $cpuLga1200],
            CategoriaComponente::PlacaMae
        );

        $this->assertTrue($compativeis->contains('id', $placaLga1200->id));
        $this->assertFalse($compativeis->contains('id', $placaAm4->id));
    }

    public function test_ram_compativel_filtra_por_tipo_suportado_pela_placa(): void
    {
        $placa = $this->componente(CategoriaComponente::PlacaMae, ['socket' => 'AM4', 'form_factor' => 'ATX', 'tipos_ram_suportados' => ['DDR4'], 'interfaces_armazenamento_suportadas' => ['SATA']]);
        $ramDdr4 = $this->componente(CategoriaComponente::Ram, ['tipo' => 'DDR4', 'capacidade_gb' => 8], 'RAM DDR4');
        $ramDdr5 = $this->componente(CategoriaComponente::Ram, ['tipo' => 'DDR5', 'capacidade_gb' => 16], 'RAM DDR5');

        $compativeis = $this->service->componentesCompativeis(
            [CategoriaComponente::PlacaMae->value => $placa],
            CategoriaComponente::Ram
        );

        $this->assertTrue($compativeis->contains('id', $ramDdr4->id));
        $this->assertFalse($compativeis->contains('id', $ramDdr5->id));
    }

    public function test_armazenamento_compativel_filtra_por_interface_suportada_pela_placa(): void
    {
        $placa = $this->componente(CategoriaComponente::PlacaMae, ['socket' => 'AM4', 'form_factor' => 'ATX', 'tipos_ram_suportados' => ['DDR4'], 'interfaces_armazenamento_suportadas' => ['SATA']]);
        $ssdSata = $this->componente(CategoriaComponente::Armazenamento, ['tipo' => 'SSD', 'interface' => 'SATA', 'capacidade_gb' => 240], 'SSD SATA');
        $ssdNvme = $this->componente(CategoriaComponente::Armazenamento, ['tipo' => 'SSD', 'interface' => 'NVMe', 'capacidade_gb' => 480], 'SSD NVMe');

        $compativeis = $this->service->componentesCompativeis(
            [CategoriaComponente::PlacaMae->value => $placa],
            CategoriaComponente::Armazenamento
        );

        $this->assertTrue($compativeis->contains('id', $ssdSata->id));
        $this->assertFalse($compativeis->contains('id', $ssdNvme->id));
    }

    public function test_gabinete_compativel_filtra_por_form_factor_da_placa(): void
    {
        $placaAtx = $this->componente(CategoriaComponente::PlacaMae, ['socket' => 'AM4', 'form_factor' => 'ATX', 'tipos_ram_suportados' => ['DDR4'], 'interfaces_armazenamento_suportadas' => ['SATA']]);
        $gabineteAtx = $this->componente(CategoriaComponente::Gabinete, ['form_factors_suportados' => ['ATX', 'Micro-ATX']], 'Gabinete ATX');
        $gabineteItx = $this->componente(CategoriaComponente::Gabinete, ['form_factors_suportados' => ['Mini-ITX']], 'Gabinete ITX');

        $compativeis = $this->service->componentesCompativeis(
            [CategoriaComponente::PlacaMae->value => $placaAtx],
            CategoriaComponente::Gabinete
        );

        $this->assertTrue($compativeis->contains('id', $gabineteAtx->id));
        $this->assertFalse($compativeis->contains('id', $gabineteItx->id));
    }

    public function test_sem_selecao_retorna_todos_os_componentes_ativos_da_categoria(): void
    {
        $this->componente(CategoriaComponente::Cpu, ['socket' => 'LGA1200'], 'CPU 1');
        $this->componente(CategoriaComponente::Cpu, ['socket' => 'AM4'], 'CPU 2');

        $compativeis = $this->service->componentesCompativeis([], CategoriaComponente::Cpu);

        $this->assertCount(2, $compativeis);
    }

    public function test_componente_inativo_nunca_aparece_como_compativel(): void
    {
        Componente::create([
            'categoria' => CategoriaComponente::Cpu,
            'nome' => 'CPU inativa',
            'specs' => ['socket' => 'LGA1200'],
            'ativo' => false,
        ]);

        $compativeis = $this->service->componentesCompativeis([], CategoriaComponente::Cpu);

        $this->assertCount(0, $compativeis);
    }

    public function test_aviso_fonte_retorna_null_quando_potencia_e_suficiente(): void
    {
        $cpu = $this->componente(CategoriaComponente::Cpu, ['socket' => 'LGA1200', 'tdp_watts' => 65]);
        $gpu = $this->componente(CategoriaComponente::Gpu, ['consumo_watts' => 170]);
        $fonte = $this->componente(CategoriaComponente::Fonte, ['potencia_watts' => 650]);

        $aviso = $this->service->avisoFontePotencia([
            CategoriaComponente::Cpu->value => $cpu,
            CategoriaComponente::Gpu->value => $gpu,
            CategoriaComponente::Fonte->value => $fonte,
        ]);

        $this->assertNull($aviso);
    }

    public function test_aviso_fonte_alerta_quando_potencia_parece_insuficiente(): void
    {
        $cpu = $this->componente(CategoriaComponente::Cpu, ['socket' => 'LGA1200', 'tdp_watts' => 65]);
        $gpu = $this->componente(CategoriaComponente::Gpu, ['consumo_watts' => 170]);
        $fonte = $this->componente(CategoriaComponente::Fonte, ['potencia_watts' => 200]);

        $aviso = $this->service->avisoFontePotencia([
            CategoriaComponente::Cpu->value => $cpu,
            CategoriaComponente::Gpu->value => $gpu,
            CategoriaComponente::Fonte->value => $fonte,
        ]);

        $this->assertNotNull($aviso);
        $this->assertStringContainsString('insuficiente', $aviso);
    }

    public function test_aviso_fonte_retorna_null_quando_nenhuma_fonte_selecionada(): void
    {
        $cpu = $this->componente(CategoriaComponente::Cpu, ['socket' => 'LGA1200', 'tdp_watts' => 65]);

        $aviso = $this->service->avisoFontePotencia([
            CategoriaComponente::Cpu->value => $cpu,
        ]);

        $this->assertNull($aviso);
    }
}
