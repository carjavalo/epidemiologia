<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpidemiologiaRegistro extends Model
{
    protected $table = 'seguimiento_microbiologico';

    protected $fillable = [
        'paciente_id',
        'tipo_id',
        // Sección 1 — Paciente
        'nombre', 'id_historia', 'fecha_nacimiento', 'sexo', 'identificador_unico',
        // Sección 2 — Microbiología
        'tipo_muestra', 'n_reporte', 'sede', 'ubicacion', 'fecha_toma_muestra',
        'microorganismo', 'cultivo_num', 'sensibles', 'intermedios', 'resistentes', 'marcadores_resistencia',
        // Sección 3 — Booleans
        'tiene_procedimiento', 'tiene_intervencion_proa', 'caso_cerrado', 'mortalidad',
        // Sección 4 — Procedimiento PROA
        'cod_episodio', 'nom_sala', 'num_cama', 'fecha_ingreso', 'nombre_eps', 'edad',
        'medico_tratante', 'cod_diag', 'cie10', 'diagnostico', 'antimicrobiano',
        'cantidad', 'presentacion', 'via_aplicacion', 'frecuencia_suministro',
        'dias_antibiotico', 'fecha_suministro',
        // Sección 5 — Intervención PROA
        'mes', 'fecha_intervencion', 'fecha_inicio_antibiotico', 'dosis_suministrada',
        'sistema_internacional', 'perfil_antimicrobiano', 'especialista_tratante',
        'diagnostico_infeccioso', 'dosis_adecuada', 'fecha_fin_antibiotico',
        'tiempo_tratamiento', 'duracion_adecuada', 'cultivo_previo', 'resultado_cultivo',
        'solicitudes_pruebas', 'oportunidad_reporte', 'indicacion_terapia', 'tratamiento',
        'valoracion_grupo1', 'valoracion_uci', 'fecha_valoracion', 'ajuste_prescripcion',
        'adherencia_proa', 'adherencia_guias', 'razon_no_adherencia', 'observacion',

        // Sección 6 — Datos Complementarios
        'pais_origen', 'departamento', 'municipio',
        'diagnostico_ingreso', 'asegurador', 'peso', 'fecha_ingreso_hosp',
        'microorganismo_2', 'microorganismo_3', 'fecha_quirurgica_previa', 'dias_entre_qx_e_infeccion',
        'categoria_quirurgica', 'egreso', 'sitio', 'tipo', 'clasificacion', 'clasificacion_texto',
        'especialidad_cirugia', 'procedimiento_quirurgico', 'bano_quirurgico', 'asepsia_quirurgica',
        'profilaxis', 'antibioticos_usados', 'asa_preoperatoria', 'tiempo_quirurgico', 'tipo_cirugia',
        'clasificacion_cirugia', 'puntaje_nnis', 'revision_equipo', 'interconsulta_infectologia',
        'comentarios', 'fecha_insercion', 'fecha_retiro',

        // Control de edición (bloqueo tras primer guardado de usuario básico)
        'edicion_bloqueada',
    ];

    protected $casts = [
        'fecha_nacimiento'        => 'date',
        'fecha_toma_muestra'      => 'date',
        'fecha_ingreso'           => 'datetime',
        'fecha_suministro'        => 'date',
        'fecha_intervencion'      => 'date',
        'fecha_inicio_antibiotico'=> 'date',
        'fecha_fin_antibiotico'   => 'date',
        'fecha_valoracion'        => 'date',
        // Booleans
        'tiene_procedimiento'     => 'boolean',
        'tiene_intervencion_proa' => 'boolean',
        'caso_cerrado'            => 'boolean',
        'mortalidad'              => 'boolean',
        'cultivo_previo'          => 'boolean',
        // Fechas de Datos Complementarios
        'fecha_ingreso_hosp'      => 'date',
        'fecha_quirurgica_previa' => 'date',
        'fecha_insercion'         => 'date',
        'fecha_retiro'            => 'date',
        'edicion_bloqueada'       => 'boolean',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}
