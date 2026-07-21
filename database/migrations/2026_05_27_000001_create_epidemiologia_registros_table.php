<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguimiento_microbiologico', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paciente_id')->nullable();

            // ══════════════════════════════════════════════════════
            //  SECCIÓN 1 — Datos básicos del paciente (Epidemiología)
            //  Pre-cargados desde el Excel de seguimiento
            // ══════════════════════════════════════════════════════
            $table->string('nombre', 255)->nullable();
            $table->string('id_historia', 50)->nullable();           // Historia clínica
            $table->date('fecha_nacimiento')->nullable();
            $table->char('sexo', 1)->nullable();                     // M / F
            $table->string('identificador_unico', 100)->nullable();  // CC / TI / CE...

            // ══════════════════════════════════════════════════════
            //  SECCIÓN 2 — Datos microbiológicos (Epidemiología)
            //  Pre-cargados desde el Excel
            // ══════════════════════════════════════════════════════
            $table->string('tipo_muestra', 150)->nullable();
            $table->string('n_reporte', 100)->nullable();
            $table->string('sede', 150)->nullable();
            $table->string('ubicacion', 255)->nullable();
            $table->date('fecha_toma_muestra')->nullable();
            $table->string('microorganismo', 255)->nullable();
            $table->text('sensibles')->nullable();
            $table->text('intermedios')->nullable();
            $table->text('resistentes')->nullable();
            $table->text('marcadores_resistencia')->nullable();

            // ══════════════════════════════════════════════════════
            //  SECCIÓN 3 — Banderas de estado (booleans)
            // ══════════════════════════════════════════════════════
            $table->boolean('tiene_procedimiento')->default(false);   // Tiene antimicrobiano registrado
            $table->boolean('tiene_intervencion_proa')->default(false); // Fue intervenido por PROA
            $table->boolean('caso_cerrado')->default(false);
            $table->boolean('mortalidad')->default(false);

            // ══════════════════════════════════════════════════════
            //  SECCIÓN 4 — Datos del procedimiento / tratamiento PROA
            //  (provenientes de deta_procedimientos)
            // ══════════════════════════════════════════════════════
            $table->integer('cod_episodio')->nullable();
            $table->string('nom_sala', 255)->nullable();
            $table->string('num_cama', 20)->nullable();
            $table->dateTime('fecha_ingreso')->nullable();
            $table->string('nombre_eps', 255)->nullable();
            $table->integer('edad')->nullable();
            $table->string('medico_tratante', 255)->nullable();
            $table->string('cod_diag', 10)->nullable();
            $table->string('cie10', 10)->nullable();
            $table->string('diagnostico', 500)->nullable();
            $table->string('antimicrobiano', 150)->nullable();
            $table->string('cantidad', 100)->nullable();
            $table->string('presentacion', 255)->nullable();
            $table->string('via_aplicacion', 100)->nullable();
            $table->string('frecuencia_suministro', 100)->nullable(); // Tiem_Horas original
            $table->string('dias_antibiotico', 100)->nullable();
            $table->date('fecha_suministro')->nullable();

            // ══════════════════════════════════════════════════════
            //  SECCIÓN 5 — Intervención PROA
            //  (provenientes de intervenciones_proa)
            // ══════════════════════════════════════════════════════
            $table->string('mes', 20)->nullable();
            $table->date('fecha_intervencion')->nullable();
            $table->date('fecha_inicio_antibiotico')->nullable();
            $table->string('dosis_suministrada', 100)->nullable();
            $table->string('sistema_internacional', 150)->nullable(); // Nombre directo
            $table->string('perfil_antimicrobiano', 255)->nullable(); // Nombre directo
            $table->string('especialista_tratante', 255)->nullable(); // Nombre directo
            $table->string('diagnostico_infeccioso', 255)->nullable(); // Nombre directo
            $table->enum('dosis_adecuada', ['Si', 'No', 'No aplica'])->nullable();
            $table->date('fecha_fin_antibiotico')->nullable();
            $table->string('tiempo_tratamiento', 100)->nullable();
            $table->enum('duracion_adecuada', ['Si', 'No', 'No aplica'])->nullable();
            $table->boolean('cultivo_previo')->default(false);
            $table->string('resultado_cultivo', 255)->nullable();     // Nombre directo
            $table->text('solicitudes_pruebas')->nullable();
            $table->string('oportunidad_reporte', 255)->nullable();
            $table->string('indicacion_terapia', 255)->nullable();    // Nombre directo
            $table->string('tratamiento', 255)->nullable();           // Nombre directo
            $table->enum('valoracion_grupo1', ['Si', 'No', 'No aplica'])->nullable();
            $table->enum('valoracion_uci', ['Si', 'No', 'No aplica'])->nullable();
            $table->date('fecha_valoracion')->nullable();
            $table->enum('ajuste_prescripcion', ['Si', 'No', 'No aplica'])->nullable();
            $table->enum('adherencia_proa', ['Si', 'No', 'Parcial', 'No aplica'])->nullable();
            $table->enum('adherencia_guias', ['Si', 'No'])->nullable();
            $table->string('razon_no_adherencia', 500)->nullable();
            $table->text('observacion')->nullable();

            $table->timestamps();

            $table->foreign('paciente_id')
                ->references('id')
                ->on('pacientes')
                ->nullOnDelete();

            // Índices para búsquedas frecuentes
            $table->index('paciente_id');
            $table->index('id_historia');
            $table->index('identificador_unico');
            $table->index('fecha_toma_muestra');
            $table->index('tiene_procedimiento');
            $table->index('caso_cerrado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimiento_microbiologico');
    }
};
