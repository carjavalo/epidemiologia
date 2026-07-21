<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255)->nullable();
            $table->string('id_historia', 50)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->char('sexo', 1)->nullable();
            $table->string('identificador_unico', 100)->nullable();
            $table->string('tipo_identificacion', 20)->nullable();
            $table->timestamps();

            $table->unique('identificador_unico');
            $table->index('id_historia');
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
