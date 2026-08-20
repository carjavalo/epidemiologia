<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Normalización de epidemiología — Fase 1.
 *
 * Separa el "caso de microorganismo" (todo lo que en el formulario va después
 * del campo Marcadores) de las muestras/cultivos, que son las filas que se
 * repiten dentro de un mismo bloque.
 *
 * Esta migración es ADITIVA: no borra ninguna columna de
 * seguimiento_microbiologico, así que el código actual sigue funcionando
 * igual mientras se completan las fases 2 y 3.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Idempotente: un intento previo alcanzó a crear la tabla pero falló al
        // añadir caso_id, dejando la migración sin registrar. Así se puede
        // reejecutar sobre ese estado a medias sin perder nada.
        if (!Schema::hasTable('casos_microorganismo')) {
        Schema::create('casos_microorganismo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paciente_id')->nullable();

            // Identificación del caso
            $table->string('microorganismo', 255)->nullable();
            // UPPER(TRIM(microorganismo)) — clave de agrupación automática
            $table->string('microorganismo_norm', 255)->nullable();
            // 'automatico' = agrupado por el importador; 'manual' = la usuaria
            // lo agrupó con los checkboxes y el importador ya no debe tocarlo.
            $table->enum('origen', ['automatico', 'manual'])->default('automatico');

            // ══════════════════════════════════════════════════════
            //  Datos complementarios del caso
            //  (se llenan una sola vez por bloque en el formulario)
            // ══════════════════════════════════════════════════════
            $table->string('tipo_id', 50)->nullable();
            $table->string('pais_origen', 255)->nullable();
            $table->string('departamento', 150)->nullable();
            $table->string('municipio', 150)->nullable();
            $table->string('diagnostico_ingreso', 500)->nullable();
            $table->string('asegurador', 255)->nullable();
            $table->decimal('peso', 8, 2)->nullable();
            $table->date('fecha_ingreso_hosp')->nullable();

            $table->date('fecha_quirurgica_previa')->nullable();
            $table->string('categoria_quirurgica', 255)->nullable();
            $table->string('egreso', 50)->nullable();
            $table->string('sitio', 255)->nullable();
            $table->string('tipo', 100)->nullable();
            $table->string('clasificacion', 100)->nullable();
            $table->string('clasificacion_texto', 255)->nullable();

            $table->string('especialidad_cirugia', 255)->nullable();
            $table->string('procedimiento_quirurgico', 500)->nullable();
            $table->string('tiempo_quirurgico', 100)->nullable();
            $table->string('bano_quirurgico', 50)->nullable();
            $table->string('asepsia_quirurgica', 50)->nullable();
            $table->string('profilaxis', 50)->nullable();
            $table->string('antibioticos_usados', 50)->nullable();
            $table->string('asa_preoperatoria', 50)->nullable();
            $table->string('tipo_cirugia', 100)->nullable();
            $table->string('clasificacion_cirugia', 100)->nullable();
            $table->string('puntaje_nnis', 100)->nullable();
            $table->string('revision_equipo', 100)->nullable();
            $table->string('interconsulta_infectologia', 50)->nullable();

            $table->date('fecha_insercion')->nullable();
            $table->date('fecha_retiro')->nullable();
            $table->text('comentarios')->nullable();

            // Control de edición (mismo criterio que seguimiento_microbiologico)
            $table->boolean('edicion_bloqueada')->default(false);
            $table->unsignedBigInteger('creado_por')->nullable();

            $table->timestamps();

            $table->foreign('paciente_id')->references('id')->on('pacientes')->nullOnDelete();
            $table->foreign('creado_por')->references('id')->on('users')->nullOnDelete();

            $table->index(['paciente_id', 'microorganismo_norm']);
            $table->index('origen');
        });
        }

        // La muestra apunta a su caso. Nullable: una muestra recién importada
        // puede quedar sin agrupar hasta que corra el agrupador.
        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            if (!Schema::hasColumn('seguimiento_microbiologico', 'caso_id')) {
                $table->unsignedBigInteger('caso_id')->nullable()->after('paciente_id');
                $table->foreign('caso_id')->references('id')->on('casos_microorganismo')->nullOnDelete();
                $table->index('caso_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            if (Schema::hasColumn('seguimiento_microbiologico', 'caso_id')) {
                $table->dropForeign(['caso_id']);
                $table->dropIndex(['caso_id']);
                $table->dropColumn('caso_id');
            }
        });

        Schema::dropIfExists('casos_microorganismo');
    }
};
