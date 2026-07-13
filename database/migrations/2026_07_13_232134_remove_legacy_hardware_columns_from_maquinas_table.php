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
            $table->dropColumn([
                'processador',
                'memoria_ram_gb',
                'tipo_armazenamento',
                'capacidade_armazenamento_gb',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maquinas', function (Blueprint $table) {
            $table->string('processador')->nullable();
            $table->unsignedInteger('memoria_ram_gb')->nullable();
            $table->string('tipo_armazenamento')->nullable();
            $table->unsignedInteger('capacidade_armazenamento_gb')->nullable();
        });
    }
};
