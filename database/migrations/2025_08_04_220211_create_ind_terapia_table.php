<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones para crear la tabla indicaciones_terapia.
     */
    public function up(): void
    {
        Schema::create('indicaciones_terapia', function (Blueprint $table) {
            $table->id(); // Primary key, BIGINT UNSIGNED, auto-increment
            $table->string('descripcion', 150); // VARCHAR(150), NOT NULL
            $table->timestamps(); // created_at y updated_at TIMESTAMP, nullable
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicaciones_terapia');
    }
};
