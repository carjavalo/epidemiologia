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
        Schema::create('esp_tratante', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 150)->comment('Descripción de la especialidad tratante');
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
        Schema::dropIfExists('esp_tratante');
    }
};
