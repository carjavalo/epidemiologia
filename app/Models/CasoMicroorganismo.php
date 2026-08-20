<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Caso de microorganismo: agrupa las muestras/cultivos de un mismo germen en
 * un paciente y guarda los datos complementarios (todo lo que en el formulario
 * va después del campo Marcadores).
 *
 * Un paciente puede tener VARIOS casos del mismo microorganismo (por ejemplo
 * una colonización previa y una reinfección posterior); por eso la agrupación
 * no es única por (paciente, microorganismo) y la usuaria puede separarlos con
 * los checkboxes del formulario.
 */
class CasoMicroorganismo extends Model
{
    protected $table = 'casos_microorganismo';

    public const ORIGEN_AUTOMATICO = 'automatico';
    public const ORIGEN_MANUAL     = 'manual';

    /**
     * Campos complementarios que vive en el caso (no en la muestra).
     * Es la lista canónica: la usan el formulario, el agrupador y la migración.
     */
    public const CAMPOS_COMPLEMENTARIOS = [
        'tipo_id',
        'pais_origen', 'departamento', 'municipio',
        'diagnostico_ingreso', 'asegurador', 'peso', 'fecha_ingreso_hosp',
        'fecha_quirurgica_previa', 'categoria_quirurgica', 'egreso', 'egreso_fuente',
        'sitio', 'tipo', 'clasificacion', 'clasificacion_texto',
        'especialidad_cirugia', 'procedimiento_quirurgico', 'tiempo_quirurgico',
        'bano_quirurgico', 'asepsia_quirurgica', 'profilaxis',
        'antibioticos_usados', 'asa_preoperatoria', 'tipo_cirugia',
        'clasificacion_cirugia', 'puntaje_nnis', 'revision_equipo',
        'interconsulta_infectologia',
        'fecha_dx_infeccion', 'dias_estancia_previos_infeccion',
        'dispositivo_notificacion', 'es_iso', 'duda', 'estado', 'modificado', 'fecha_reporte_hospital_seguro',
        'fecha_insercion', 'fecha_retiro', 'comentarios',
    ];

    protected $fillable = [
        'paciente_id', 'microorganismo', 'microorganismo_norm', 'origen',
        'tipo_id',
        'pais_origen', 'departamento', 'municipio',
        'diagnostico_ingreso', 'asegurador', 'peso', 'fecha_ingreso_hosp',
        'fecha_quirurgica_previa', 'categoria_quirurgica', 'egreso', 'egreso_fuente',
        'sitio', 'tipo', 'clasificacion', 'clasificacion_texto',
        'especialidad_cirugia', 'procedimiento_quirurgico', 'tiempo_quirurgico',
        'bano_quirurgico', 'asepsia_quirurgica', 'profilaxis',
        'antibioticos_usados', 'asa_preoperatoria', 'tipo_cirugia',
        'clasificacion_cirugia', 'puntaje_nnis', 'revision_equipo',
        'interconsulta_infectologia',
        'fecha_dx_infeccion', 'dias_estancia_previos_infeccion',
        'dispositivo_notificacion', 'es_iso', 'duda', 'estado', 'modificado', 'fecha_reporte_hospital_seguro',
        'fecha_insercion', 'fecha_retiro', 'comentarios',
        'edicion_bloqueada', 'creado_por',
    ];

    protected $casts = [
        'fecha_ingreso_hosp'      => 'date',
        'fecha_quirurgica_previa' => 'date',
        'fecha_dx_infeccion'      => 'date',
        'fecha_insercion'         => 'date',
        'fecha_retiro'            => 'date',
        'fecha_reporte_hospital_seguro' => 'date',
        'peso'                    => 'decimal:2',
        'edicion_bloqueada'       => 'boolean',
    ];

    /**
     * Clave de agrupación automática a partir del nombre del microorganismo.
     */
    public static function normalizar(?string $microorganismo): string
    {
        return mb_strtoupper(trim((string) $microorganismo), 'UTF-8');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Muestras/cultivos que componen el caso (las secciones "Registro #n").
     */
    public function muestras()
    {
        return $this->hasMany(EpidemiologiaRegistro::class, 'caso_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Un caso agrupado a mano deja de ser candidato para el importador.
     */
    public function esManual(): bool
    {
        return $this->origen === self::ORIGEN_MANUAL;
    }
}
