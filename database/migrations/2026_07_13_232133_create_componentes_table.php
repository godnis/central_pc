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
        Schema::create('componentes', function (Blueprint $table) {
            $table->id();
            $table->string('categoria', 32);
            $table->string('nome');
            $table->string('fabricante')->nullable();
            $table->json('specs')->default('{}');
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('componentes');
    }
};
