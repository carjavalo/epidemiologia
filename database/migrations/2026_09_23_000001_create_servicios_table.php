<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de servicios (ubicaciones estandarizadas).
 *
 * Hasta ahora los servicios salían directamente del texto crudo de la columna
 * `ubicacion` de los datos importados, que llega con decenas de variantes para
 * el mismo sitio ("UCI 2", "UCI2", "UNIDAD DE CUIDADO INTENSIVO 2"). Este
 * catálogo guarda el nombre único y oficial de cada servicio; las variantes
 * viven en la tabla `equivalencias`.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('servicios')) {
            return;
        }

        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
