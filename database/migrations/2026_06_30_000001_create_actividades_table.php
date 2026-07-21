<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Historial de actividades de usuarios (trazabilidad).
     * Cada fila representa una acción realizada por un usuario sobre
     * los datos del sistema (epidemiología, PROA, importaciones, etc.).
     */
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_nombre')->nullable();      // snapshot del nombre por si se elimina el usuario
            $table->string('tipo')->default('general');     // epidemiologia | proa | importacion | general
            $table->string('accion');                        // crear | actualizar | importar ...
            $table->string('descripcion', 500);              // texto legible
            $table->string('referencia_tabla')->nullable();  // tabla afectada
            $table->unsignedBigInteger('referencia_id')->nullable(); // id del registro afectado
            $table->string('paciente')->nullable();          // contexto (nombre / documento)
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index('tipo');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
