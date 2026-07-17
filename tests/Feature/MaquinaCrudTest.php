<?php

namespace Tests\Feature;

use App\Enums\CategoriaComponente;
use App\Enums\RoleUsuario;
use App\Models\Componente;
use App\Models\Maquina;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaquinaCrudTest extends TestCase
{
    use RefreshDatabase;

    private function componentesCompativeis(): array
    {
        $cpu = Componente::create([
            'categoria' => CategoriaComponente::Cpu,
            'nome' => 'CPU Teste',
            'specs' => ['socket' => 'AM4'],
            'ativo' => true,
        ]);

        $placaMae = Componente::create([
            'categoria' => CategoriaComponente::PlacaMae,
            'nome' => 'Placa Teste',
            'specs' => [
                'socket' => 'AM4',
                'form_factor' => 'ATX',
                'tipos_ram_suportados' => ['DDR4'],
                'interfaces_armazenamento_suportadas' => ['SATA'],
            ],
            'ativo' => true,
        ]);

        $ram = Componente::create([
            'categoria' => CategoriaComponente::Ram,
            'nome' => 'RAM Teste',
            'specs' => ['tipo' => 'DDR4', 'capacidade_gb' => 8],
            'ativo' => true,
        ]);

        $armazenamento = Componente::create([
            'categoria' => CategoriaComponente::Armazenamento,
            'nome' => 'SSD Teste',
            'specs' => ['tipo' => 'SSD', 'interface' => 'SATA', 'capacidade_gb' => 240],
            'ativo' => true,
        ]);

        return compact('cpu', 'placaMae', 'ram', 'armazenamento');
    }

    private function dadosComponentes(array $c): array
    {
        return [
            'cpu' => $c['cpu']->id,
            'placa_mae' => $c['placaMae']->id,
            'ram' => [['componente_id' => $c['ram']->id, 'quantidade' => 1]],
            'armazenamento' => [['componente_id' => $c['armazenamento']->id, 'quantidade' => 1]],
        ];
    }

    public function test_usuario_autenticado_ve_a_listagem(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Admin]);

        $this->actingAs($user)->get(route('maquinas.index'))->assertOk();
    }

    public function test_convidado_e_redirecionado_para_login(): void
    {
        $this->get(route('maquinas.index'))->assertRedirect(route('login'));
    }

    public function test_cria_maquina_com_componentes_compativeis(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Admin]);
        $setor = Setor::create(['nome' => 'TI']);
        $c = $this->componentesCompativeis();

        $response = $this->actingAs($user)->post(route('maquinas.store'), [
            'nome' => 'PC-01',
            'setor_id' => $setor->id,
            'status' => 'ativa',
            'componentes' => $this->dadosComponentes($c),
        ]);

        $response->assertRedirect(route('maquinas.index'));
        $this->assertDatabaseHas('maquinas', ['nome' => 'PC-01']);

        $maquina = Maquina::where('nome', 'PC-01')->firstOrFail();
        $this->assertCount(4, $maquina->maquinaComponentes);
        $this->assertDatabaseHas('atividades', ['loggable_id' => $maquina->id, 'acao' => 'criado']);
    }

    public function test_nao_cria_maquina_com_componentes_incompativeis(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Admin]);
        $setor = Setor::create(['nome' => 'TI']);
        $c = $this->componentesCompativeis();

        $placaIncompativel = Componente::create([
            'categoria' => CategoriaComponente::PlacaMae,
            'nome' => 'Placa Incompatível',
            'specs' => ['socket' => 'LGA1200', 'form_factor' => 'ATX', 'tipos_ram_suportados' => ['DDR4'], 'interfaces_armazenamento_suportadas' => ['SATA']],
            'ativo' => true,
        ]);

        $dados = $this->dadosComponentes($c);
        $dados['placa_mae'] = $placaIncompativel->id;

        $response = $this->actingAs($user)->post(route('maquinas.store'), [
            'nome' => 'PC-02',
            'setor_id' => $setor->id,
            'status' => 'ativa',
            'componentes' => $dados,
        ]);

        $response->assertSessionHasErrors('componentes.placa_mae');
        $this->assertDatabaseMissing('maquinas', ['nome' => 'PC-02']);
    }

    public function test_atualiza_maquina(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Admin]);
        $setor = Setor::create(['nome' => 'TI']);
        $c = $this->componentesCompativeis();

        $maquina = Maquina::create(['nome' => 'PC-03', 'setor_id' => $setor->id, 'status' => 'ativa']);

        $response = $this->actingAs($user)->put(route('maquinas.update', $maquina), [
            'nome' => 'PC-03-Renomeado',
            'setor_id' => $setor->id,
            'status' => 'manutencao',
            'componentes' => $this->dadosComponentes($c),
        ]);

        $response->assertRedirect(route('maquinas.index'));
        $this->assertDatabaseHas('maquinas', ['id' => $maquina->id, 'nome' => 'PC-03-Renomeado', 'status' => 'manutencao']);
    }

    public function test_exclusao_e_soft_delete_e_pode_ser_restaurada(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Admin]);
        $setor = Setor::create(['nome' => 'TI']);
        $maquina = Maquina::create(['nome' => 'PC-04', 'setor_id' => $setor->id, 'status' => 'ativa']);

        $this->actingAs($user)->delete(route('maquinas.destroy', $maquina))
            ->assertRedirect(route('maquinas.index'));

        $this->assertSoftDeleted('maquinas', ['id' => $maquina->id]);

        $this->actingAs($user)->post(route('maquinas.restaurar', $maquina->id))
            ->assertRedirect(route('maquinas.lixeira'));

        $this->assertDatabaseHas('maquinas', ['id' => $maquina->id, 'deleted_at' => null]);
    }

    public function test_usuario_leitura_nao_pode_criar_nem_excluir(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Leitura]);
        $setor = Setor::create(['nome' => 'TI']);
        $maquina = Maquina::create(['nome' => 'PC-05', 'setor_id' => $setor->id, 'status' => 'ativa']);

        $this->actingAs($user)->get(route('maquinas.create'))->assertForbidden();
        $this->actingAs($user)->delete(route('maquinas.destroy', $maquina))->assertForbidden();
        $this->actingAs($user)->get(route('maquinas.index'))->assertOk();
    }

    public function test_tecnico_pode_criar_mas_nao_excluir(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Tecnico]);
        $setor = Setor::create(['nome' => 'TI']);
        $maquina = Maquina::create(['nome' => 'PC-06', 'setor_id' => $setor->id, 'status' => 'ativa']);

        $this->actingAs($user)->get(route('maquinas.create'))->assertOk();
        $this->actingAs($user)->delete(route('maquinas.destroy', $maquina))->assertForbidden();
    }
}
