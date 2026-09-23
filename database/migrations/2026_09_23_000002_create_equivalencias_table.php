<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Equivalencias: texto crudo de la fuente externa -> valor estandarizado.
 *
 * La fuente de datos escribe el mismo servicio o el mismo tipo de muestra de
 * muchas maneras ("PANEL NEUMONIA", "PNAEL NEUMONIA", "Lavado Boncroalveolar"
 * son todos "2. Cepillado o LBA"). Aquí se guarda cada variante conocida.
 *
 * La columna `clave` es el texto crudo normalizado (sin acentos, en mayúsculas,
 * con la puntuación convertida en espacios y separando letra de número, de modo
 * que "UCI2" y "UCI 2" caen en la misma clave). Es lo que se busca al importar;
 * `texto_crudo` se conserva solo para mostrarlo tal cual llegó.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('equivalencias')) {
            return;
        }

        Schema::create('equivalencias', function (Blueprint $table) {
            $table->id();
            // 'servicio' o 'muestra'
            $table->string('catalogo', 20);
            $table->string('clave', 190);
            $table->string('texto_crudo', 190);
            $table->string('valor', 150);
            $table->timestamps();

            // Un mismo texto no puede apuntar a dos valores dentro del mismo catálogo.
            $table->unique(['catalogo', 'clave']);
            $table->index(['catalogo', 'valor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equivalencias');
    }
};
