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
        Schema::create('plantillas_observaciones', function (Blueprint $table) {
            $table->id(); // Campo entero, clave primaria, auto-incrementable, único
            $table->string('unico')->unique(); // Campo para identificador único del perfil
            $table->text('descripcion'); // Campo de tipo texto de máximo 65535 caracteres
            $table->timestamps(); // created_at y updated_at estándar de Laravel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantillas_observaciones');
    }
};
