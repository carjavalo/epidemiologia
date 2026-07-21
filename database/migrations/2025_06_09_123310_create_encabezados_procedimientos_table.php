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
        Schema::create('encabezados_procedimientos', function (Blueprint $table) {
            $table->id('id_procedimiento');
            $table->dateTime('fecha_procedimiento');
            $table->string('Nom_procedimiento', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encabezados_procedimientos');
    }
};
