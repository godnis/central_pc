<?php

namespace Tests\Feature;

use App\Enums\RoleUsuario;
use App\Models\Maquina;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetorCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_cria_setor(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Admin]);

        $this->actingAs($user)->post(route('setores.store'), ['nome' => 'Financeiro'])
            ->assertRedirect(route('setores.index'));

        $this->assertDatabaseHas('setores', ['nome' => 'Financeiro']);
        $this->assertDatabaseHas('atividades', ['acao' => 'criado']);
    }

    public function test_atualiza_setor(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Admin]);
        $setor = Setor::create(['nome' => 'TI']);

        $this->actingAs($user)->put(route('setores.update', $setor), ['nome' => 'Tecnologia'])
            ->assertRedirect(route('setores.index'));

        $this->assertDatabaseHas('setores', ['id' => $setor->id, 'nome' => 'Tecnologia']);
    }

    public function test_exclui_setor_sem_maquinas(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Admin]);
        $setor = Setor::create(['nome' => 'TI']);

        $this->actingAs($user)->delete(route('setores.destroy', $setor))
            ->assertRedirect(route('setores.index'));

        $this->assertDatabaseMissing('setores', ['id' => $setor->id]);
    }

    public function test_nao_exclui_setor_com_maquinas(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Admin]);
        $setor = Setor::create(['nome' => 'TI']);
        Maquina::create(['nome' => 'PC-01', 'setor_id' => $setor->id, 'status' => 'ativa']);

        $this->actingAs($user)->delete(route('setores.destroy', $setor))
            ->assertRedirect(route('setores.index'));

        $this->assertDatabaseHas('setores', ['id' => $setor->id]);
    }

    public function test_usuario_leitura_nao_pode_criar_setor(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Leitura]);

        $this->actingAs($user)->get(route('setores.create'))->assertForbidden();
    }
}
