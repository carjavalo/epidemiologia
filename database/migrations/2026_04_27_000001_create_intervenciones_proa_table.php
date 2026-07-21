<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intervenciones_proa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_deta_procedimiento'); // FK a deta_procedimientos.id

            // Campos del PROA (manuales, llenados por el médico)
            $table->string('mes', 20)->nullable();
            $table->date('fecha_intervencion')->nullable();
            $table->date('fecha_inicio_antibiotico')->nullable();

            // Dosis
            $table->string('dosis_suministrada', 100)->nullable();
            $table->unsignedBigInteger('id_sis_internacional')->nullable();
            $table->unsignedBigInteger('id_frecuencia')->nullable();
            $table->unsignedBigInteger('id_perfil_antimicrobiano')->nullable();
            $table->unsignedBigInteger('id_esp_tratante')->nullable();
            $table->unsignedBigInteger('id_diag_infeccioso')->nullable();

            // Adecuación
            $table->enum('dosis_adecuada', ['Si', 'No', 'No aplica'])->nullable();
            $table->date('fecha_fin_antibiotico')->nullable();
            $table->string('tiempo_tratamiento', 100)->nullable();
            $table->enum('duracion_adecuada', ['Si', 'No', 'No aplica'])->nullable();

            // Cultivo
            $table->enum('cultivo_previo', ['Si', 'No'])->nullable();
            $table->date('fecha_muestra')->nullable();
            $table->unsignedBigInteger('id_tipo_muestra')->nullable();
            $table->unsignedBigInteger('id_resultado')->nullable();
            $table->unsignedBigInteger('id_microorganismo')->nullable();
            $table->unsignedBigInteger('id_pantimicrobiano')->nullable();

            // Pruebas especiales
            $table->text('solicitudes_pruebas')->nullable();
            $table->string('oportunidad_reporte', 255)->nullable();

            // Terapia
            $table->unsignedBigInteger('id_indicacion_terapia')->nullable();
            $table->unsignedBigInteger('id_tratamiento')->nullable();

            // Valoración infectología
            $table->enum('valoracion_grupo1', ['Si', 'No', 'No aplica'])->nullable();
            $table->enum('valoracion_uci', ['Si', 'No', 'No aplica'])->nullable();
            $table->date('fecha_valoracion')->nullable();

            // Seguimiento
            $table->enum('ajuste_prescripcion', ['Si', 'No', 'No aplica'])->nullable();
            $table->enum('adherencia_proa', ['Si', 'No', 'Parcial', 'No aplica'])->nullable();
            $table->enum('adherencia_guias', ['Si', 'No'])->nullable();
            $table->string('razon_no_adherencia', 500)->nullable();
            $table->enum('caso_cerrado', ['Si', 'No'])->nullable();
            $table->enum('mortalidad', ['Si', 'No'])->nullable();
            $table->text('observacion')->nullable();

            $table->timestamps();

            $table->foreign('id_sis_internacional')->references('id')->on('sis_internacional')->nullOnDelete();
            $table->foreign('id_frecuencia')->references('id')->on('frecuencia')->nullOnDelete();
            $table->foreign('id_perfil_antimicrobiano')->references('id')->on('perfiles')->nullOnDelete();
            $table->foreign('id_esp_tratante')->references('id')->on('esp_tratante')->nullOnDelete();
            $table->foreign('id_diag_infeccioso')->references('id')->on('diag_infecciosos')->nullOnDelete();
            $table->foreign('id_tipo_muestra')->references('id')->on('tip_muestras')->nullOnDelete();
            $table->foreign('id_resultado')->references('id')->on('resultados')->nullOnDelete();
            $table->foreign('id_microorganismo')->references('id')->on('microorganismos')->nullOnDelete();
            $table->foreign('id_pantimicrobiano')->references('id')->on('pantimicrobiano')->nullOnDelete();
            $table->foreign('id_indicacion_terapia')->references('id')->on('indicaciones_terapia')->nullOnDelete();
            $table->foreign('id_tratamiento')->references('id')->on('tratamientos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intervenciones_proa');
    }
};
