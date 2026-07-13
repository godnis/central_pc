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
        Schema::create('maquina_componentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquina_id')->constrained('maquinas')->cascadeOnDelete();
            $table->foreignId('componente_id')->constrained('componentes')->cascadeOnDelete();
            $table->unsignedInteger('quantidade')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maquina_componentes');
    }
};
