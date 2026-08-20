<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Campos exclusivos del perfil ENFERMERO (req. 7, 8 y 11):
 *  - dispositivo_notificacion: ¿infección asociada a dispositivo de notificación
 *    obligatoria? (SI/NO) → si SÍ, se diligencian fecha_insercion / fecha_retiro.
 *  - es_iso: ¿corresponde a una ISO (infección de sitio quirúrgico)? (SI/NO).
 *  - duda / estado / modificado / fecha_reporte_hospital_seguro: se muestran solo
 *    cuando SITIO ≠ "No aplica".
 */
return new class extends Migration
{
    private array $columnas = [
        'dispositivo_notificacion'       => ['string', 10],
        'es_iso'                         => ['string', 10],
        'duda'                           => ['string', 10],
        'estado'                         => ['string', 20],
        'modificado'                     => ['string', 10],
        'fecha_reporte_hospital_seguro'  => ['date', null],
    ];

    public function up(): void
    {
        foreach (['seguimiento_microbiologico', 'casos_microorganismo'] as $tabla) {
            if (!Schema::hasTable($tabla)) {
                continue;
            }
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                foreach ($this->columnas as $col => [$tipo, $largo]) {
                    if (Schema::hasColumn($tabla, $col)) {
                        continue;
                    }
                    if ($tipo === 'date') {
                        $table->date($col)->nullable();
                    } else {
                        $table->string($col, $largo)->nullable();
                    }
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
                foreach (array_keys($this->columnas) as $col) {
                    if (Schema::hasColumn($tabla, $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
