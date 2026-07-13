<?php

namespace Database\Seeders;

use App\Enums\CategoriaComponente;
use App\Models\Componente;
use App\Models\Maquina;
use App\Models\Setor;
use Illuminate\Database\Seeder;

class MaquinaSeeder extends Seeder
{
    /** @var array<string, Componente> componente CPU já criado, por nome */
    private array $cpusPorNome = [];

    /** @var array<int, Componente> componente RAM já criado, por capacidade em GB */
    private array $ramsPorGb = [];

    /** @var array<string, Componente> componente de armazenamento já criado, por "tipo|gb" */
    private array $armazenamentosPorChave = [];

    /**
     * Mapeia a sigla do setor como aparece na planilha de inventário para o
     * nome exato já cadastrado em SetorSeeder (organograma oficial).
     */
    private array $setoresExistentes = [
        'DAO' => 'DAO - Diretoria de Atendimento e Orientação',
        'POMADA' => 'APV - Assessoria da Pomada do Vovô',
        'DAF' => 'DAF - Diretoria Administrativa e Financeira',
        'DIADM' => 'DIADM - Divisão Administrativa DAF',
        'DPS' => 'DPS - Diretoria da Promoção Social',
        'OUVIDORIA' => 'Ouvidoria',
        'DED' => 'DED - Diretoria de Estudos Doutrinários',
        'DAE' => 'DAE - Diretoria de Assistência Espiritual',
        'ACE' => 'ACE - Assessoria de Comunicação e Eventos',
        'AME' => 'AME - Assessoria de Estudos e Aplicações de Medicina Espiritual',
        'DAC' => 'DAC - Diretoria de Arte e Cultura',
        'DIJ' => 'DIJ - Diretoria de Infância e Juventude',
        'Presidência' => 'Presidência',
        'ATI' => 'ATI - Assessoria de Tecnologia da Informação',
    ];

    /**
     * Setores que aparecem na planilha mas não existem no organograma —
     * serão criados na hora com um nome mais legível.
     */
    private array $setoresNovos = [
        'BAZ' => 'BAZ - Bazar',
        'SALA DE PASSE' => 'Sala de Passe',
        'MANUTENÇÃO' => 'Manutenção',
        'Aud.' => 'Auditório',
        'LIVRARIA' => 'Livraria',
    ];

    /**
     * Run the database seeds.
     *
     * Inventário de máquinas levantado a partir da planilha de controle interno.
     * Linhas com dado essencial ilegível/ausente (processador ou setor em
     * branco) foram deixadas de fora e deverão ser complementadas depois.
     */
    public function run(): void
    {
        $maquinas = [
            // Recepção
            ['Recepção', 'recepcao-1', 'Dual Core', null, 'SSD', 240, '(conferir processador)'],
            ['Recepção', 'recepcao-2', 'Phenom', 8, 'SSD', 240, '(conferir processador)'],
            ['Recepção', 'recepcao-3', 'I5-3330', 8, 'SSD', 240, null],
            ['Recepção', 'recepcao-4', 'Dual Core', 4, 'SSD', 240, '(conferir processador)'],
            ['Recepção', 'recepcao-5', 'I5-2400', 4, 'SSD', 240, 'Integrada'],
            ['Recepção', 'recepcao-6', 'Phenom', 8, 'SSD', 240, 'Integrada (conferir processador)'],

            // DAO
            ['DAO', 'DAO-1', 'I5-3330', 8, 'SSD', 112, null],
            ['DAO', 'DAO-6', 'Phenom', 4, 'SSD', 240, '(conferir processador)'],
            ['DAO', 'DAO-8', 'I5-3330', 8, 'SSD', 112, null],

            // POMADA
            ['POMADA', 'POM-1', 'Phenom', 4, 'SSD', 120, '(conferir processador)'],

            // BAZ
            ['BAZ', 'BAZ-6', 'Ryzen 5 46', 8, 'SSD', 112, '(conferir processador, sigla cortada na planilha)'],
            ['BAZ', 'BAZ-8', 'Ryzen 5 46', 8, 'SSD', 112, '(conferir processador, sigla cortada na planilha)'],
            ['BAZ', 'BAZ-5', 'Ryzen 5 46', 8, 'HD', 1130, '(conferir processador; armazenamento original: 1.13 TB)'],
            ['BAZ', 'BAZ-1', 'I5-4440', 8, 'SSD', 224, null],
            ['BAZ', 'BAZ-4', 'I5-3330', 4, 'SSD', 224, null],
            ['BAZ', 'BAZ-2', 'I7-3770K', 8, 'SSD', 112, null],
            ['BAZ', 'BAZ-3', 'I7-3770K', 8, 'SSD', 112, null],
            ['BAZ', 'BAZ-7', 'I5-3330', 12, 'SSD', 112, null],
            ['BAZ', 'BAZ-10', 'I3-7100', 8, 'SSD', 110, null],

            // DAF
            ['DAF', 'DAF-3', 'I7-7770', 16, 'SSD', 447, null],
            ['DAF', 'DAF-4', 'I5-4460', 12, 'SSD', 112, null],
            ['DAF', 'DAF-1', 'Ryzen 5 46', 16, 'SSD', 477, '(conferir processador, sigla cortada na planilha)'],

            // DIADM
            ['DIADM', 'DIADM-1', 'Core 2 Duo', 4, 'SSD', 112, '(conferir processador)'],
            ['DIADM', 'DIADM-2', 'I3-530', 5, 'SSD', 222, null],
            ['DIADM', 'DIADM-3', 'Phenom', 8, 'SSD', 222, '(conferir processador)'],
            ['DIADM', 'DIADM-4', 'I5-2400', 12, 'SSD', 224, null],

            // Produtora
            ['Produtora', 'PROD-1', 'Ryzen 7 57', 32, 'SSD', 477, 'RTX 3060 / 192.168.1.8 (conferir processador)'],
            ['Produtora', 'PROD-2', 'I9-11900K', 32, 'SSD', 466, 'RTX 3060 / 192.168.1.5'],
            ['Produtora', 'PROD-10', 'Ryzen 5 57', 16, 'SSD', 466, 'RX 580 / 192.168.1.48 (conferir processador)'],
            ['Produtora', 'PROD-4', 'I9-11900K', 64, 'SSD', 466, 'RTX 3060'],
            ['Produtora', 'PROD-6', 'Ryzen 7 57', 32, 'SSD', 464, 'RTX 3060 (+ armazenamento adicional; conferir processador)'],
            ['Produtora', 'PROD-5', 'I9-11900K', 32, 'SSD', 464, 'RTX 3060 (+ armazenamento adicional)'],

            // Sala de Passe
            ['SALA DE PASSE', 'CABINE-5', 'I5-3330', 8, 'SSD', 224, null],
            ['SALA DE PASSE', 'CABINE-1', 'I5-2400', 4, 'SSD', 224, null],
            ['SALA DE PASSE', 'CABINE-2', 'I5-2500', 6, 'SSD', 224, null],
            ['SALA DE PASSE', 'CABINE-3', 'I5-3330', 4, 'SSD', 224, null],
            ['SALA DE PASSE', 'CABINE-4', 'I5-3330', 4, 'SSD', 224, null],

            // Almoxarifado
            ['Almoxarifado', 'ALMOX-5', 'Ryzen 5 46', 16, 'SSD', 238, '(conferir processador, sigla cortada na planilha)'],
            ['Almoxarifado', 'ALMOX-3', 'I5-3570', 8, 'SSD', 224, null],
            ['Almoxarifado', 'ALMOX-4', 'I3-4160', 8, 'SSD', 224, null],

            // DPS
            ['DPS', 'DPS-1', 'I5-3330', 8, 'SSD', 224, null],
            ['DPS', 'DPS-5', 'I5-3330', 8, 'SSD', 111, null],
            ['DPS', 'DPS-3', 'Ryzen 5 46', null, 'SSD', 476, '(conferir processador e RAM, dados incompletos na planilha)'],
            ['DPS', 'DPS-6', 'FX 8320', 16, 'SSD', 111, null],
            ['DPS', 'DPS-8', 'I5-3330', 8, 'SSD', 224, null],
            ['DPS', 'DPS-7', 'I3-2100', 3, 'SSD', 112, null],
            ['DPS', 'DPS-9', 'Phenom X', 6, 'SSD', 224, '(conferir processador)'],
            ['DPS', 'DPS-2', 'Phenom', 4, 'SSD', 110, '(conferir processador)'],

            // Manutenção
            ['MANUTENÇÃO', 'MAN-2', 'Core 2 Quad', 4, 'SSD', 476, '(conferir processador)'],

            // Ouvidoria
            ['OUVIDORIA', 'OUV-1', 'Phenom X', 4, 'HD', 222, '(conferir processador)'],

            // DED
            ['DED', 'DED-2', 'I5-3330', 6, 'SSD', 222, null],
            ['DED', 'DED-3', 'I5-2500', 4, 'SSD', 112, null],
            ['DED', 'DED-6', 'I5-3330', 8, 'SSD', 222, null],
            ['DED', 'DED-5', 'I5-3330', 4, 'SSD', 222, null],
            ['DED', 'DED-4', 'I5-2400', 4, 'SSD', 222, null],

            // DAE
            ['DAE', 'DAE-6', 'Core 2 Duo', 4, 'SSD', 222, '(conferir processador)'],
            ['DAE', 'DAE-7', 'I5-2400', 4, 'SSD', 222, null],
            ['DAE', 'DAE-9', 'I5-750', 8, 'SSD', 222, null],
            ['DAE', 'DAE-2', 'I5-3330', 8, 'SSD', 222, null],
            ['DAE', 'DAE-8', 'I5-2400', 4, 'SSD', 222, null],
            ['DAE', 'DAE-3', 'Phenom', 4, 'SSD', 222, '(conferir processador)'],

            // ACE
            ['ACE', 'ACE-1', 'I5-3550S', 8, 'SSD', 222, null],

            // Editora
            ['EDITORA', 'EDI-1', 'I5-3330', 4, 'SSD', 222, null],

            // AME
            ['AME', 'AME-1', 'Phenom', 4, 'SSD', 223, '(conferir processador)'],

            // DAC
            ['DAC', 'DAC-01', 'I9-11900K', 32, 'SSD', 446, 'RTX 3060'],

            // DIJ
            ['DIJ', 'DIJ-01', 'Core 2 Quad', 4, 'SSD', 240, '(conferir processador)'],

            // Auditório
            ['Aud.', 'Comunhão-Aud', 'I7-3770K', 12, 'SSD', 110, '(conferir identificação e processador)'],

            // Presidência
            ['Presidência', 'PRES-2', 'I5-4570', 8, 'SSD', 222, null],
            ['Presidência', 'PRES-3', 'I5-3330', 8, 'SSD', 222, null],
            ['Presidência', 'PRES-1', 'I5-3330', 8, 'SSD', 224, null],

            // Guarita
            ['Guarita', 'GUARITA-1', 'Core 2 Quad', 4, 'SSD', 120, '(conferir identificação e processador)'],
            ['Guarita', 'GUARITA-2', 'I7-4330', 16, 'SSD', 112, 'GTX 1050 Ti'],

            // ATI
            ['ATI', 'ATI-5', 'I5-12400', 16, 'SSD', 220, null],
            ['ATI', 'ATI-6', 'I5-12400', 16, 'SSD', 480, null],
            ['ATI', 'ATI-12', 'I5-9400F', 16, 'SSD', 447, 'RX 560'],
            ['ATI', 'ATI-4', 'I5-12400', 16, 'SSD', 446, 'GTX 1050 Ti'],
            ['ATI', 'ATI-8', 'I7-10610U', 32, 'SSD', 477, null],
            ['ATI', 'ATI-1', 'I5-12400', 16, 'SSD', 447, 'GTX 760'],

            // Livraria
            ['LIVRARIA', 'LIV-5', 'I7-3770K', 6, 'SSD', 477, 'GT 710'],
            ['LIVRARIA', 'LIV-COMUNHA', 'I5-9400F', 8, 'SSD', 244, '(conferir identificação e processador — linha pouco legível na planilha)'],
            ['LIVRARIA', 'LIV-COMUNHA-2', 'Ryzen 5 4500', 16, 'SSD', 244, 'RX 6500XT (conferir identificação e processador — linha pouco legível na planilha)'],
            ['LIVRARIA', 'LIV-2', 'I5-3330', 8, 'SSD', 224, null],
            ['LIVRARIA', 'MAN-004', 'i3 m370', 4, 'HD', 477, null],
            ['LIVRARIA', 'Liv-3', 'I3-2100', null, 'SSD', 112, '(conferir RAM, dado ausente na planilha)'],
        ];

        foreach ($maquinas as [$setorLabel, $nome, $processador, $ram, $tipo, $capacidade, $observacoes]) {
            $maquina = Maquina::create([
                'nome' => $nome,
                'setor_id' => $this->resolverSetor($setorLabel)->id,
                'sistema_operacional' => null,
                'observacoes' => $observacoes,
            ]);

            $this->vincularHardwareLegado($maquina, $processador, $ram, $tipo, $capacidade);
        }
    }

    /**
     * Converte os campos de texto livre da planilha original em componentes
     * do catálogo (mesma lógica da migration de backfill), já que a coluna
     * livre não existe mais em `maquinas`. Placa-mãe não é criada — não há
     * esse dado na planilha original.
     */
    private function vincularHardwareLegado(Maquina $maquina, ?string $processador, ?int $ram, ?string $tipo, ?int $capacidade): void
    {
        if (filled($processador)) {
            $nome = trim($processador);
            $cpu = $this->cpusPorNome[$nome] ??= Componente::firstOrCreate(
                ['categoria' => CategoriaComponente::Cpu->value, 'nome' => $nome],
                ['specs' => [], 'ativo' => true]
            );

            $maquina->maquinaComponentes()->create(['componente_id' => $cpu->id, 'quantidade' => 1]);
        }

        if (! is_null($ram)) {
            $componenteRam = $this->ramsPorGb[$ram] ??= Componente::firstOrCreate(
                ['categoria' => CategoriaComponente::Ram->value, 'nome' => "{$ram}GB (genérico, a completar)"],
                ['specs' => ['capacidade_gb' => $ram], 'ativo' => true]
            );

            $maquina->maquinaComponentes()->create(['componente_id' => $componenteRam->id, 'quantidade' => 1]);
        }

        if (filled($tipo) && ! is_null($capacidade)) {
            $chave = "{$tipo}|{$capacidade}";
            $armazenamento = $this->armazenamentosPorChave[$chave] ??= Componente::firstOrCreate(
                ['categoria' => CategoriaComponente::Armazenamento->value, 'nome' => "{$tipo} {$capacidade}GB (genérico, a completar)"],
                ['specs' => ['tipo' => $tipo, 'capacidade_gb' => $capacidade], 'ativo' => true]
            );

            $maquina->maquinaComponentes()->create(['componente_id' => $armazenamento->id, 'quantidade' => 1]);
        }
    }

    /**
     * Busca o setor já cadastrado correspondente à sigla da planilha, ou cria
     * um novo se a sigla não existir no organograma oficial.
     */
    private function resolverSetor(string $rotulo): Setor
    {
        if (isset($this->setoresExistentes[$rotulo])) {
            return Setor::where('nome', $this->setoresExistentes[$rotulo])->firstOrFail();
        }

        $nome = $this->setoresNovos[$rotulo] ?? $rotulo;

        return Setor::firstOrCreate(['nome' => $nome]);
    }
}
