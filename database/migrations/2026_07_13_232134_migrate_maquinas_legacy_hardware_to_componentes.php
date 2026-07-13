<?php

use App\Enums\CategoriaComponente;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Backfill: converte os campos de texto livre de hardware das máquinas já
     * cadastradas em Componentes do novo catálogo, sem perder o dado bruto.
     * As specs ficam vazias/parciais (não dá pra inferir socket, tipo de RAM
     * etc. a partir só do texto legado) — alguém completa depois pela tela
     * de administração do catálogo. Placa-mãe não existia como campo, então
     * nenhuma é criada aqui.
     */
    public function up(): void
    {
        $maquinas = DB::table('maquinas')->get([
            'id', 'processador', 'memoria_ram_gb', 'tipo_armazenamento', 'capacidade_armazenamento_gb',
        ]);

        $cpusPorNome = [];
        $ramsPorGb = [];
        $armazenamentosPorChave = [];

        $agora = now();

        foreach ($maquinas as $maquina) {
            if (filled($maquina->processador)) {
                $nome = trim($maquina->processador);
                $cpuId = $cpusPorNome[$nome] ??= DB::table('componentes')->insertGetId([
                    'categoria' => CategoriaComponente::Cpu->value,
                    'nome' => $nome,
                    'specs' => json_encode([]),
                    'ativo' => true,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ]);

                DB::table('maquina_componentes')->insert([
                    'maquina_id' => $maquina->id,
                    'componente_id' => $cpuId,
                    'quantidade' => 1,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ]);
            }

            if (! is_null($maquina->memoria_ram_gb)) {
                $gb = (int) $maquina->memoria_ram_gb;
                $ramId = $ramsPorGb[$gb] ??= DB::table('componentes')->insertGetId([
                    'categoria' => CategoriaComponente::Ram->value,
                    'nome' => "{$gb}GB (genérico, a completar)",
                    'specs' => json_encode(['capacidade_gb' => $gb]),
                    'ativo' => true,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ]);

                DB::table('maquina_componentes')->insert([
                    'maquina_id' => $maquina->id,
                    'componente_id' => $ramId,
                    'quantidade' => 1,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ]);
            }

            if (filled($maquina->tipo_armazenamento) && ! is_null($maquina->capacidade_armazenamento_gb)) {
                $tipo = $maquina->tipo_armazenamento;
                $gb = (int) $maquina->capacidade_armazenamento_gb;
                $chave = "{$tipo}|{$gb}";

                $armazenamentoId = $armazenamentosPorChave[$chave] ??= DB::table('componentes')->insertGetId([
                    'categoria' => CategoriaComponente::Armazenamento->value,
                    'nome' => "{$tipo} {$gb}GB (genérico, a completar)",
                    'specs' => json_encode(['tipo' => $tipo, 'capacidade_gb' => $gb]),
                    'ativo' => true,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ]);

                DB::table('maquina_componentes')->insert([
                    'maquina_id' => $maquina->id,
                    'componente_id' => $armazenamentoId,
                    'quantidade' => 1,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * Limpeza best-effort: esta migration só roda uma vez sobre dados que,
     * neste ponto do histórico, são os únicos vínculos existentes — por isso
     * o rollback simplesmente esvazia as duas tabelas em vez de tentar
     * rastrear exatamente o que foi inserido.
     */
    public function down(): void
    {
        DB::table('maquina_componentes')->truncate();
        DB::table('componentes')->truncate();
    }
};
