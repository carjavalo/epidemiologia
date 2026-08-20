<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\DiagInfeccioso;
use App\Models\EpidemiologiaRegistro;
use App\Models\EspTratante;
use App\Models\IndicacionTerapia;
use App\Models\IntervencionProa;
use App\Models\Perfil;
use App\Models\Procedimiento;
use App\Models\Resultado;
use App\Models\SisInternacional;
use App\Models\Tratamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntervencionProaController extends Controller
{
    /**
     * Guarda o actualiza una intervención PROA y la replica en
     * la tabla maestra `seguimiento_microbiologico`.
     */
    public function guardar(Request $request)
    {
        $request->validate([
            'id_deta_procedimiento' => 'required|integer|exists:deta_procedimientos,id',
            'via_aplicacion'        => 'nullable|string|max:100',
        ]);

        // La vía de administración vive en deta_procedimientos (no en
        // intervenciones_proa). Se persiste aparte para no romper el
        // updateOrCreate de la intervención.
        $viaAplicacion = $request->input('via_aplicacion');

        $datos = $request->only([
            'id_deta_procedimiento',
            'mes', 'fecha_intervencion', 'fecha_inicio_antibiotico',
            'dosis_suministrada',
            'id_sis_internacional', 'id_frecuencia', 'id_perfil_antimicrobiano',
            'id_esp_tratante', 'id_diag_infeccioso',
            'dosis_adecuada', 'fecha_fin_antibiotico', 'tiempo_tratamiento',
            'duracion_adecuada',
            'cultivo_previo', 'fecha_muestra',
            'id_tipo_muestra', 'id_resultado', 'id_microorganismo', 'id_pantimicrobiano',
            'solicitudes_pruebas', 'oportunidad_reporte',
            'id_indicacion_terapia', 'id_tratamiento',
            'valoracion_grupo1', 'valoracion_uci', 'fecha_valoracion',
            'ajuste_prescripcion', 'adherencia_proa', 'adherencia_guias',
            'razon_no_adherencia',
            'caso_cerrado', 'mortalidad', 'observacion',
        ]);

        $fks = [
            'id_sis_internacional', 'id_frecuencia', 'id_perfil_antimicrobiano',
            'id_esp_tratante', 'id_diag_infeccioso', 'id_tipo_muestra',
            'id_resultado', 'id_microorganismo', 'id_pantimicrobiano',
            'id_indicacion_terapia', 'id_tratamiento',
        ];
        foreach ($fks as $fk) {
            if (empty($datos[$fk])) {
                $datos[$fk] = null;
            }
        }

        // Control de edición. Un básico queda bloqueado tras registrar, salvo
        // que tenga el permiso "puede editar registros" (o sea admin).
        $esAdmin = optional($request->user())->esAdmin();
        $puedeEditar = (bool) optional($request->user())->puedeEditarProa();
        $existente = IntervencionProa::where('id_deta_procedimiento', $datos['id_deta_procedimiento'])->first();
        if (!$puedeEditar && $existente && $existente->edicion_bloqueada) {
            return response()->json([
                'success' => false,
                'message' => 'Esta intervención PROA ya fue registrada. No tienes permiso para modificarla.',
            ], 403);
        }

        $intervencion = DB::transaction(function () use ($datos, $viaAplicacion, $request) {
            $interv = IntervencionProa::updateOrCreate(
                ['id_deta_procedimiento' => $datos['id_deta_procedimiento']],
                $datos
            );

            // Guardar la vía de administración en la dosis (deta_procedimientos),
            // para que persista y se refleje en el seguimiento.
            if ($request->has('via_aplicacion')) {
                Procedimiento::where('id', $datos['id_deta_procedimiento'])
                    ->update(['Via_Aplicacion' => $viaAplicacion]);
            }

            $this->reflejarEnSeguimiento($datos['id_deta_procedimiento'], $interv);

            return $interv;
        });

        // Si lo guardó un usuario básico, se bloquea para futuras ediciones suyas.
        if (!$esAdmin) {
            $intervencion->update(['edicion_bloqueada' => true]);
        }

        // Trazabilidad: registrar la actividad del usuario
        $proc = Procedimiento::find($datos['id_deta_procedimiento']);
        Actividad::registrar([
            'tipo'             => 'proa',
            'accion'           => $intervencion->wasRecentlyCreated ? 'crear' : 'actualizar',
            'descripcion'      => 'Intervención PROA'
                                  . ($proc && $proc->Antimicrobiano ? ' — ' . $proc->Antimicrobiano : ''),
            'referencia_tabla' => 'intervenciones_proa',
            'referencia_id'    => $intervencion->id,
            'paciente'         => $proc ? ('Doc. ' . $proc->Num_Ident . ($proc->Hist_Clinica ? ' · HC ' . $proc->Hist_Clinica : '')) : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Intervención guardada correctamente.',
            'id'      => $intervencion->id,
        ]);
    }

    /**
     * Replica procedimiento + intervención PROA en el seguimiento
     * microbiológico más reciente del paciente.
     */
    private function reflejarEnSeguimiento(int $idDetaProc, IntervencionProa $interv): void
    {
        $proc = Procedimiento::find($idDetaProc);
        if (!$proc) return;

        $seguimiento = EpidemiologiaRegistro::query()
            ->where(function ($q) use ($proc) {
                $q->where('identificador_unico', $proc->Num_Ident)
                  ->orWhere('id_historia', $proc->Hist_Clinica);
            })
            ->orderByDesc('fecha_toma_muestra')
            ->first();

        $catalogos = array_filter([
            'sistema_internacional'  => SisInternacional::find($interv->id_sis_internacional)?->descripcion,
            'perfil_antimicrobiano'  => Perfil::find($interv->id_perfil_antimicrobiano)?->descripcion,
            'especialista_tratante'  => EspTratante::find($interv->id_esp_tratante)?->descripcion,
            'diagnostico_infeccioso' => DiagInfeccioso::find($interv->id_diag_infeccioso)?->descripcion,
            'indicacion_terapia'     => IndicacionTerapia::find($interv->id_indicacion_terapia)?->descripcion,
            'tratamiento'            => Tratamiento::find($interv->id_tratamiento)?->descripcion,
            'resultado_cultivo'      => Resultado::find($interv->id_resultado)?->descripcion,
        ], fn ($v) => !is_null($v));

        $payload = array_merge([
            // Sección 4 — Procedimiento PROA
            'cod_episodio'          => $proc->Cod_Episodio,
            'nom_sala'              => $proc->Nom_Sala,
            'num_cama'              => $proc->Num_Cama,
            'fecha_ingreso'         => $proc->F_Ingreso,
            'nombre_eps'            => $proc->Nom_Eps,
            'edad'                  => $proc->Edad,
            'medico_tratante'       => $proc->Medico_Trata,
            'cod_diag'              => $proc->Cod_Diag,
            'cie10'                 => $proc->CIE10,
            'diagnostico'           => $proc->Diagnostico,
            'antimicrobiano'        => $proc->Antimicrobiano,
            'cantidad'              => $proc->Cantidad,
            'presentacion'          => $proc->Presentacion,
            'via_aplicacion'        => $proc->Via_Aplicacion,
            'frecuencia_suministro' => $proc->Tiem_Horas,
            'dias_antibiotico'      => $proc->Dias_Antibioticos,
            'fecha_suministro'      => $proc->Fec_Sumistro,

            // Sección 5 — Intervención PROA
            'mes'                      => $interv->mes,
            'fecha_intervencion'       => $interv->fecha_intervencion,
            'fecha_inicio_antibiotico' => $interv->fecha_inicio_antibiotico,
            'dosis_suministrada'       => $interv->dosis_suministrada,
            'dosis_adecuada'           => $interv->dosis_adecuada,
            'fecha_fin_antibiotico'    => $interv->fecha_fin_antibiotico,
            'tiempo_tratamiento'       => $interv->tiempo_tratamiento,
            'duracion_adecuada'        => $interv->duracion_adecuada,
            'cultivo_previo'           => in_array($interv->cultivo_previo, ['Si', true, 1, '1'], true),
            'solicitudes_pruebas'      => $interv->solicitudes_pruebas,
            'oportunidad_reporte'      => $interv->oportunidad_reporte,
            'valoracion_grupo1'        => $interv->valoracion_grupo1,
            'valoracion_uci'           => $interv->valoracion_uci,
            'fecha_valoracion'         => $interv->fecha_valoracion,
            'ajuste_prescripcion'      => $interv->ajuste_prescripcion,
            'adherencia_proa'          => $interv->adherencia_proa,
            'adherencia_guias'         => $interv->adherencia_guias,
            'razon_no_adherencia'      => $interv->razon_no_adherencia,
            'observacion'              => $interv->observacion,

            // Banderas
            'tiene_procedimiento'     => true,
            'tiene_intervencion_proa' => true,
            'caso_cerrado'            => in_array($interv->caso_cerrado, ['Si', true, 1, '1'], true),
            'mortalidad'              => in_array($interv->mortalidad, ['Si', true, 1, '1'], true),
        ], $catalogos);

        if ($seguimiento) {
            $seguimiento->update($payload);
        } else {
            EpidemiologiaRegistro::create(array_merge([
                'nombre'              => $proc->Medico_Trata,
                'id_historia'         => $proc->Hist_Clinica,
                'identificador_unico' => $proc->Num_Ident,
                'ubicacion'           => $proc->Nom_Sala,
            ], $payload));
        }
    }

    public function obtener($idDetaProcedimiento)
    {
        $intervencion = IntervencionProa::where('id_deta_procedimiento', $idDetaProcedimiento)->first();
        return response()->json(['success' => true, 'data' => $intervencion]);
    }
}
