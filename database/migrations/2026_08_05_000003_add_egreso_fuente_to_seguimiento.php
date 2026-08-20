<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Deja el campo EGRESO preparado para el cruce futuro con la base de datos de
 * mortalidad del hospital (req. 6). El marcador `egreso_fuente` distingue el
 * desenlace capturado a mano ('manual') del actualizado automáticamente por el
 * cruce ('mortalidad'), para que la sincronización no pise lo ya registrado.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['seguimiento_microbiologico', 'casos_microorganismo'] as $tabla) {
            if (!Schema::hasTable($tabla) || Schema::hasColumn($tabla, 'egreso_fuente')) {
                continue;
            }
            Schema::table($tabla, function (Blueprint $table) {
                // 'manual' | 'mortalidad' | null
                $table->string('egreso_fuente', 20)->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['seguimiento_microbiologico', 'casos_microorganismo'] as $tabla) {
            if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, 'egreso_fuente')) {
                Schema::table($tabla, function (Blueprint $table) {
                    $table->dropColumn('egreso_fuente');
                });
            }
        }
    }
};
