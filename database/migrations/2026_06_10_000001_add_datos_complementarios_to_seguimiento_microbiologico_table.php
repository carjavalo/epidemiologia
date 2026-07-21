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
        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            $table->string('procedencia', 255)->nullable();
            $table->string('diagnostico_ingreso', 500)->nullable();
            $table->string('asegurador', 255)->nullable();
            $table->decimal('peso', 8, 2)->nullable();
            $table->date('fecha_ingreso_hosp')->nullable();
            $table->string('microorganismo_2', 255)->nullable();
            $table->string('microorganismo_3', 255)->nullable();
            $table->date('fecha_quirurgica_previa')->nullable();
            $table->integer('dias_entre_qx_e_infeccion')->nullable();
            $table->string('categoria_quirurgica', 255)->nullable();
            $table->string('egreso', 50)->nullable();
            $table->string('sitio', 255)->nullable();
            $table->string('tipo', 100)->nullable();
            $table->string('clasificacion', 100)->nullable();
            $table->string('clasificacion_texto', 255)->nullable();
            $table->string('especialidad_cirugia', 255)->nullable();
            $table->string('procedimiento_quirurgico', 500)->nullable();
            $table->string('bano_quirurgico', 50)->nullable();
            $table->string('asepsia_quirurgica', 50)->nullable();
            $table->string('profilaxis', 50)->nullable();
            $table->string('antibioticos_usados', 50)->nullable();
            $table->string('asa_preoperatoria', 50)->nullable();
            $table->string('tiempo_quirurgico', 100)->nullable();
            $table->string('tipo_cirugia', 100)->nullable();
            $table->string('clasificacion_cirugia', 100)->nullable();
            $table->string('puntaje_nnis', 100)->nullable();
            $table->string('revision_equipo', 100)->nullable();
            $table->string('interconsulta_infectologia', 50)->nullable();
            $table->text('comentarios')->nullable();
            $table->date('fecha_insercion')->nullable();
            $table->date('fecha_retiro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            $table->dropColumn([
                'procedencia',
                'diagnostico_ingreso',
                'asegurador',
                'peso',
                'fecha_ingreso_hosp',
                'microorganismo_2',
                'microorganismo_3',
                'fecha_quirurgica_previa',
                'dias_entre_qx_e_infeccion',
                'categoria_quirurgica',
                'egreso',
                'sitio',
                'tipo',
                'clasificacion',
                'clasificacion_texto',
                'especialidad_cirugia',
                'procedimiento_quirurgico',
                'bano_quirurgico',
                'asepsia_quirurgica',
                'profilaxis',
                'antibioticos_usados',
                'asa_preoperatoria',
                'tiempo_quirurgico',
                'tipo_cirugia',
                'clasificacion_cirugia',
                'puntaje_nnis',
                'revision_equipo',
                'interconsulta_infectologia',
                'comentarios',
                'fecha_insercion',
                'fecha_retiro'
            ]);
        });
    }
};
