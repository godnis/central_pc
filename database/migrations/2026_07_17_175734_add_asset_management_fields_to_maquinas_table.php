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
            $table->string('patrimonio')->nullable()->unique()->after('nome');
            $table->string('status', 20)->default('ativa')->after('setor_id');
            $table->string('responsavel')->nullable()->after('observacoes');
            $table->date('data_aquisicao')->nullable()->after('responsavel');
            $table->string('foto_path')->nullable()->after('data_aquisicao');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maquinas', function (Blueprint $table) {
            $table->dropColumn(['patrimonio', 'status', 'responsavel', 'data_aquisicao', 'foto_path']);
            $table->dropSoftDeletes();
        });
    }
};
