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
        Schema::create('tip_muestras', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 150)->comment('Descripción del tipo de muestra');
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tip_muestras');
    }
};
