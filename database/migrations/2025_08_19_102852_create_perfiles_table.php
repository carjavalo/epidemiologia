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
        Schema::create('perfiles', function (Blueprint $table) {
            $table->id(); // Campo entero, clave primaria, auto-incrementable, único
            $table->string('descripcion', 150); // Campo VARCHAR(150), no nulo
            $table->timestamps(); // created_at y updated_at estándar de Laravel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfiles');
    }
};
