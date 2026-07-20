<?php

namespace Tests\Feature;

use App\Enums\RoleUsuario;
use App\Models\ItemDescarte;
use App\Models\RemessaDescarte;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DescarteTest extends TestCase
{
    use RefreshDatabase;

    public function test_tecnico_cadastra_peca_na_fila(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Tecnico]);

        $response = $this->actingAs($user)->post(route('descarte.store'), [
            'descricao' => 'Memória RAM DDR3 4GB Kingston',
            'diagnostico' => 'Não liga / não é reconhecida',
        ]);

        $response->assertRedirect(route('descarte.index'));
        $this->assertDatabaseHas('itens_descarte', [
            'descricao' => 'Memória RAM DDR3 4GB Kingston',
            'remessa_id' => null,
            'user_id' => $user->id,
        ]);
    }

    public function test_cadastro_exige_descricao_e_diagnostico(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Tecnico]);

        $this->actingAs($user)->post(route('descarte.store'), [
            'descricao' => '',
            'diagnostico' => '',
        ])->assertSessionHasErrors(['descricao', 'diagnostico']);
    }

    public function test_leitura_nao_pode_cadastrar_peca(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Leitura]);

        $this->actingAs($user)->post(route('descarte.store'), [
            'descricao' => 'Fonte 450W',
            'diagnostico' => 'Não liga',
        ])->assertForbidden();

        $this->actingAs($user)->get(route('descarte.index'))->assertOk();
    }

    public function test_remove_item_ainda_na_fila(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Tecnico]);
        $item = ItemDescarte::create([
            'descricao' => 'HD 500GB',
            'diagnostico' => 'Não é lido',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->delete(route('descarte.destroy', $item))
            ->assertRedirect(route('descarte.index'));

        $this->assertDatabaseMissing('itens_descarte', ['id' => $item->id]);
    }

    public function test_nao_remove_item_ja_despachado_em_remessa(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Tecnico]);
        $remessa = RemessaDescarte::create(['devolvido_por' => $user->name, 'user_id' => $user->id]);
        $item = ItemDescarte::create([
            'descricao' => 'Placa-mãe queimada',
            'diagnostico' => 'Não liga',
            'user_id' => $user->id,
            'remessa_id' => $remessa->id,
        ]);

        $this->actingAs($user)->delete(route('descarte.destroy', $item))
            ->assertRedirect(route('descarte.index'));

        $this->assertDatabaseHas('itens_descarte', ['id' => $item->id]);
    }

    public function test_gerar_remessa_exige_ao_menos_um_item(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Tecnico]);

        $this->actingAs($user)->post(route('descarte.remessas.store'), [
            'itens' => [],
        ])->assertSessionHasErrors('itens');
    }

    public function test_gerar_remessa_marca_itens_selecionados_e_congela_devolvido_por(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Tecnico, 'name' => 'Fulano de Tal']);
        $item1 = ItemDescarte::create(['descricao' => 'RAM DDR3', 'diagnostico' => 'Não liga', 'user_id' => $user->id]);
        $item2 = ItemDescarte::create(['descricao' => 'HD 500GB', 'diagnostico' => 'Não é lido', 'user_id' => $user->id]);
        $foraDaSelecao = ItemDescarte::create(['descricao' => 'Fonte 450W', 'diagnostico' => 'Não liga', 'user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('descarte.remessas.store'), [
            'itens' => [$item1->id, $item2->id],
        ]);

        $remessa = RemessaDescarte::firstOrFail();
        $response->assertRedirect(route('descarte.remessas.show', $remessa));

        $this->assertSame('Fulano de Tal', $remessa->devolvido_por);
        $this->assertSame($remessa->id, $item1->fresh()->remessa_id);
        $this->assertSame($remessa->id, $item2->fresh()->remessa_id);
        $this->assertNull($foraDaSelecao->fresh()->remessa_id);
    }

    public function test_gerar_remessa_rejeita_item_que_ja_esta_em_outra_remessa(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Tecnico]);
        $remessaAnterior = RemessaDescarte::create(['devolvido_por' => $user->name, 'user_id' => $user->id]);
        $item = ItemDescarte::create([
            'descricao' => 'GPU queimada',
            'diagnostico' => 'Sem vídeo',
            'user_id' => $user->id,
            'remessa_id' => $remessaAnterior->id,
        ]);

        $this->actingAs($user)->post(route('descarte.remessas.store'), [
            'itens' => [$item->id],
        ])->assertSessionHasErrors('itens.0');
    }

    public function test_pagina_de_impressao_pagina_a_cada_vinte_itens(): void
    {
        $user = User::factory()->create(['role' => RoleUsuario::Tecnico]);
        $remessa = RemessaDescarte::create(['devolvido_por' => $user->name, 'user_id' => $user->id]);

        for ($i = 0; $i < 25; $i++) {
            ItemDescarte::create([
                'descricao' => "Peça {$i}",
                'diagnostico' => 'Não funciona',
                'user_id' => $user->id,
                'remessa_id' => $remessa->id,
            ]);
        }

        $response = $this->actingAs($user)->get(route('descarte.remessas.show', $remessa));

        $response->assertOk();
        $response->assertSeeText('Peça 0');
        $response->assertSeeText('Peça 24');
        $this->assertSame(2, substr_count($response->getContent(), 'class="anexo-pagina'));
    }
}
