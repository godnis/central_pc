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
        Schema::create('maquinas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('setor_id')->constrained('setores');
            $table->string('sistema_operacional')->nullable();
            $table->string('processador');
            $table->unsignedInteger('memoria_ram_gb')->nullable();
            $table->enum('tipo_armazenamento', ['HD', 'SSD']);
            $table->unsignedInteger('capacidade_armazenamento_gb');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maquinas');
    }
};
