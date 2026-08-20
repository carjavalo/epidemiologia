<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FECHA DE REPORTE (req. 9): dato por registro de muestra, va justo después de
 * FECHA DE TOMA DE MUESTRA y es obligatorio (incluso si SITIO = No aplica).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seguimiento_microbiologico')
            && !Schema::hasColumn('seguimiento_microbiologico', 'fecha_reporte')) {
            Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
                $table->date('fecha_reporte')->nullable()->after('fecha_toma_muestra');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('seguimiento_microbiologico')
            && Schema::hasColumn('seguimiento_microbiologico', 'fecha_reporte')) {
            Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
                $table->dropColumn('fecha_reporte');
            });
        }
    }
};
