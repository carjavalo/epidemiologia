<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // "nacionalidad" pasa a llamarse "pais_origen".
        if (Schema::hasColumn('seguimiento_microbiologico', 'nacionalidad')
            && !Schema::hasColumn('seguimiento_microbiologico', 'pais_origen')) {
            Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
                $table->renameColumn('nacionalidad', 'pais_origen');
            });
        }

        // Procedencia = departamento + municipio.
        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            if (!Schema::hasColumn('seguimiento_microbiologico', 'departamento')) {
                $table->string('departamento', 150)->nullable();
            }
            if (!Schema::hasColumn('seguimiento_microbiologico', 'municipio')) {
                $table->string('municipio', 150)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            if (Schema::hasColumn('seguimiento_microbiologico', 'departamento')) {
                $table->dropColumn('departamento');
            }
            if (Schema::hasColumn('seguimiento_microbiologico', 'municipio')) {
                $table->dropColumn('municipio');
            }
        });

        if (Schema::hasColumn('seguimiento_microbiologico', 'pais_origen')
            && !Schema::hasColumn('seguimiento_microbiologico', 'nacionalidad')) {
            Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
                $table->renameColumn('pais_origen', 'nacionalidad');
            });
        }
    }
};
