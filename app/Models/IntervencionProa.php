<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntervencionProa extends Model
{
    use HasFactory;

    protected $table = 'intervenciones_proa';

    protected $fillable = [
        'id_deta_procedimiento',
        'mes',
        'fecha_intervencion',
        'fecha_inicio_antibiotico',
        'dosis_suministrada',
        'id_sis_internacional',
        'id_frecuencia',
        'id_perfil_antimicrobiano',
        'id_esp_tratante',
        'id_diag_infeccioso',
        'dosis_adecuada',
        'fecha_fin_antibiotico',
        'tiempo_tratamiento',
        'duracion_adecuada',
        'cultivo_previo',
        'fecha_muestra',
        'id_tipo_muestra',
        'id_resultado',
        'id_microorganismo',
        'id_pantimicrobiano',
        'solicitudes_pruebas',
        'oportunidad_reporte',
        'id_indicacion_terapia',
        'id_tratamiento',
        'valoracion_grupo1',
        'valoracion_uci',
        'fecha_valoracion',
        'ajuste_prescripcion',
        'adherencia_proa',
        'adherencia_guias',
        'razon_no_adherencia',
        'caso_cerrado',
        'mortalidad',
        'observacion',
        'edicion_bloqueada',
    ];

    protected $casts = [
        'fecha_intervencion'      => 'date',
        'fecha_inicio_antibiotico'=> 'date',
        'fecha_fin_antibiotico'   => 'date',
        'fecha_muestra'           => 'date',
        'fecha_valoracion'        => 'date',
        'edicion_bloqueada'       => 'boolean',
    ];

    public function detaProcedimiento()
    {
        return $this->belongsTo(Procedimiento::class, 'id_deta_procedimiento', 'id');
    }

    public function sisInternacional()  { return $this->belongsTo(SisInternacional::class, 'id_sis_internacional'); }
    public function frecuencia()        { return $this->belongsTo(Frecuencia::class, 'id_frecuencia'); }
    public function perfilAntimicrobiano() { return $this->belongsTo(Perfil::class, 'id_perfil_antimicrobiano'); }
    public function espTratante()       { return $this->belongsTo(EspTratante::class, 'id_esp_tratante'); }
    public function diagInfeccioso()    { return $this->belongsTo(DiagInfeccioso::class, 'id_diag_infeccioso'); }
    public function tipoMuestra()       { return $this->belongsTo(TipMuestra::class, 'id_tipo_muestra'); }
    public function resultado()         { return $this->belongsTo(Resultado::class, 'id_resultado'); }
    public function microorganismo()    { return $this->belongsTo(Microorganismo::class, 'id_microorganismo'); }
    public function pantimicrobiano()   { return $this->belongsTo(Pantimicrobiano::class, 'id_pantimicrobiano'); }
    public function indicacionTerapia() { return $this->belongsTo(IndicacionTerapia::class, 'id_indicacion_terapia'); }
    public function tratamiento()       { return $this->belongsTo(Tratamiento::class, 'id_tratamiento'); }
}
