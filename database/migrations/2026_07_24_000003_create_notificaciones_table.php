<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bandeja de notificaciones. Se usa, entre otras cosas, para avisar cuando la
 * importación detecta un nuevo curso de antibiótico (a más de 7 días del curso
 * anterior del mismo fármaco en el mismo paciente).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 30)->default('proa');   // proa, epidemiologia, sistema
            $table->string('titulo', 180);
            $table->text('mensaje');
            $table->string('url', 500)->nullable();          // enlace opcional al hacer clic
            $table->boolean('leida')->default(false);
            $table->string('referencia_tabla', 60)->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->timestamps();

            $table->index('leida');
            $table->index('created_at');
            $table->index(['referencia_tabla', 'referencia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
