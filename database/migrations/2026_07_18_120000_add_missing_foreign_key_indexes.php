<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('maquinas', function (Blueprint $table) {
            $table->index('setor_id');
        });

        Schema::table('maquina_componentes', function (Blueprint $table) {
            $table->index('maquina_id');
            $table->index('componente_id');
        });

        Schema::table('atividades', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('itens_descarte', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('remessa_id');
        });

        Schema::table('remessas_descarte', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('maquinas', function (Blueprint $table) {
            $table->dropIndex(['setor_id']);
        });

        Schema::table('maquina_componentes', function (Blueprint $table) {
            $table->dropIndex(['maquina_id']);
            $table->dropIndex(['componente_id']);
        });

        Schema::table('atividades', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('itens_descarte', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['remessa_id']);
        });

        Schema::table('remessas_descarte', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
};
