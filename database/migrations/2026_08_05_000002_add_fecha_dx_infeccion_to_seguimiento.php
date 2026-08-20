<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['seguimiento_microbiologico', 'casos_microorganismo'] as $tabla) {
            if (!Schema::hasTable($tabla)) {
                continue;
            }
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (!Schema::hasColumn($tabla, 'fecha_dx_infeccion')) {
                    $table->date('fecha_dx_infeccion')->nullable();
                }
                if (!Schema::hasColumn($tabla, 'dias_estancia_previos_infeccion')) {
                    $table->integer('dias_estancia_previos_infeccion')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['seguimiento_microbiologico', 'casos_microorganismo'] as $tabla) {
            if (!Schema::hasTable($tabla)) {
                continue;
            }
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                foreach (['fecha_dx_infeccion', 'dias_estancia_previos_infeccion'] as $col) {
                    if (Schema::hasColumn($tabla, $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
