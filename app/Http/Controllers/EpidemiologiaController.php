<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\EpidemiologiaRegistro;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EpidemiologiaController extends Controller
{
    /**
     * Campos propios de cada muestra: se repiten una vez por cada registro
     * duplicado del mismo microorganismo dentro de un bloque del formulario.
     */
    private const CAMPOS_MUESTRA = [
        'tipo_muestra', 'n_reporte', 'cultivo_num', 'sede', 'ubicacion',
        'fecha_toma_muestra', 'microorganismo', 'sensibles', 'intermedios',
        'resistentes', 'marcadores_resistencia',
    ];

    /**
     * Guardar o actualizar un registro de seguimiento microbiológico.
     * Si viene id se actualiza, si no se crea nuevo.
     */
    public function guardar(Request $request)
    {
        $validated = $request->validate([
            // Paciente
            'nombre'                  => 'nullable|string|max:255',
            'id_historia'             => 'nullable|string|max:50',
            'fecha_nacimiento'        => 'nullable|date',
            'sexo'                    => 'nullable|string|max:20',
            'identificador_unico'     => 'nullable|string|max:100',
            'tipo_id'                 => 'nullable|string|max:50',
            // Microbiología
            'tipo_muestra'            => 'nullable|string|max:150',
            'n_reporte'               => 'nullable|string|max:100',
            'cultivo_num'             => 'nullable|string|max:50',
            'sede'                    => 'nullable|string|max:150',
            'ubicacion'               => 'nullable|string|max:255',
            'fecha_toma_muestra'      => 'nullable|date',
            'microorganismo'          => 'nullable|string|max:255',
            'sensibles'               => 'nullable|string',
            'intermedios'             => 'nullable|string',
            'resistentes'             => 'nullable|string',
            'marcadores_resistencia'  => 'nullable|string',
            // Booleans
            'tiene_procedimiento'     => 'nullable|boolean',
            'tiene_intervencion_proa' => 'nullable|boolean',
            'caso_cerrado'            => 'nullable|boolean',
            'mortalidad'              => 'nullable|boolean',
            'cultivo_previo'          => 'nullable|boolean',
            // Procedimiento
            'cod_episodio'            => 'nullable|integer',
            'nom_sala'                => 'nullable|string|max:255',
            'num_cama'                => 'nullable|string|max:20',
            'fecha_ingreso'           => 'nullable|date',
            'nombre_eps'              => 'nullable|string|max:255',
            'edad'                    => 'nullable|integer',
            'medico_tratante'         => 'nullable|string|max:255',
            'cod_diag'                => 'nullable|string|max:10',
            'cie10'                   => 'nullable|string|max:10',
            'diagnostico'             => 'nullable|string|max:500',
            'antimicrobiano'          => 'nullable|string|max:150',
            'cantidad'                => 'nullable|string|max:100',
            'presentacion'            => 'nullable|string|max:255',
            'via_aplicacion'          => 'nullable|string|max:100',
            'frecuencia_suministro'   => 'nullable|string|max:100',
            'dias_antibiotico'        => 'nullable|string|max:100',
            'fecha_suministro'        => 'nullable|date',
            // Intervención PROA
            'mes'                     => 'nullable|string|max:20',
            'fecha_intervencion'      => 'nullable|date',
            'fecha_inicio_antibiotico'=> 'nullable|date',
            'dosis_suministrada'      => 'nullable|string|max:100',
            'sistema_internacional'   => 'nullable|string|max:150',
            'perfil_antimicrobiano'   => 'nullable|string|max:255',
            'especialista_tratante'   => 'nullable|string|max:255',
            'diagnostico_infeccioso'  => 'nullable|string|max:255',
            'dosis_adecuada'          => 'nullable|in:Si,No,No aplica',
            'fecha_fin_antibiotico'   => 'nullable|date',
            'tiempo_tratamiento'      => 'nullable|string|max:100',
            'duracion_adecuada'       => 'nullable|in:Si,No,No aplica',
            'resultado_cultivo'       => 'nullable|string|max:255',
            'solicitudes_pruebas'     => 'nullable|string',
            'oportunidad_reporte'     => 'nullable|string|max:255',
            'indicacion_terapia'      => 'nullable|string|max:255',
            'tratamiento'             => 'nullable|string|max:255',
            'valoracion_grupo1'       => 'nullable|in:Si,No,No aplica',
            'valoracion_uci'          => 'nullable|in:Si,No,No aplica',
            'fecha_valoracion'        => 'nullable|date',
            'ajuste_prescripcion'     => 'nullable|in:Si,No,No aplica',
            'adherencia_proa'         => 'nullable|in:Si,No,Parcial,No aplica',
            'adherencia_guias'        => 'nullable|in:Si,No',
            'razon_no_adherencia'     => 'nullable|string|max:500',
            'observacion'             => 'nullable|string',
            // Datos Complementarios
            'pais_origen'             => 'nullable|string|max:255',
            'departamento'            => 'nullable|string|max:150',
            'municipio'               => 'nullable|string|max:150',
            'diagnostico_ingreso'     => 'nullable|string|max:500',
            'asegurador'              => 'nullable|string|max:255',
            'peso'                    => 'nullable|numeric',
            'fecha_ingreso_hosp'      => 'nullable|date',
            'microorganismo_2'        => 'nullable|string|max:255',
            'microorganismo_3'        => 'nullable|string|max:255',
            'fecha_quirurgica_previa' => 'nullable|date',
            'dias_entre_qx_e_infeccion'=> 'nullable|integer',
            'categoria_quirurgica'    => 'nullable|string|max:255',
            'egreso'                  => 'nullable|string|max:50',
            'sitio'                   => 'nullable|string|max:255',
            'tipo'                    => 'nullable|string|max:100',
            'clasificacion'           => 'nullable|string|max:100',
            'clasificacion_texto'     => 'nullable|string|max:255',
            'especialidad_cirugia'    => 'nullable|string|max:255',
            'procedimiento_quirurgico'=> 'nullable|string|max:500',
            'bano_quirurgico'         => 'nullable|string|max:50',
            'asepsia_quirurgica'      => 'nullable|string|max:50',
            'profilaxis'              => 'nullable|string|max:50',
            'antibioticos_usados'     => 'nullable|string|max:50',
            'asa_preoperatoria'       => 'nullable|string|max:50',
            'tiempo_quirurgico'       => 'nullable|string|max:100',
            'tipo_cirugia'            => 'nullable|string|max:100',
            'clasificacion_cirugia'   => 'nullable|string|max:100',
            'puntaje_nnis'            => 'nullable|string|max:100',
            'revision_equipo'         => 'nullable|string|max:100',
            'interconsulta_infectologia'=> 'nullable|string|max:50',
            'comentarios'             => 'nullable|string',
            'fecha_insercion'         => 'nullable|date',
            'fecha_retiro'            => 'nullable|date',
        ]);

        // Calcular dias_entre_qx_e_infeccion y clasificacion_texto en backend si corresponde
        if ($request->has('tipo') || $request->has('clasificacion') || $request->has('sitio')) {
            $tipo = $request->input('tipo');
            $clasificacion = $request->input('clasificacion');
            $sitio = $request->input('sitio');
            if (!$tipo || !$clasificacion) {
                $validated['clasificacion_texto'] = '';
            } else {
                $texto = 'No aplica';
                if ($tipo == '1') {
                    if ($clasificacion == '1') {
                        $texto = 'Colonización Intrahospitalaria';
                    } elseif ($clasificacion == '2') {
                        $texto = 'Contaminado Intrahospitalaria';
                    } elseif ($clasificacion == '3') {
                        $texto = 'Infección';
                    } elseif ($clasificacion == '4') {
                        if ($sitio === '51. Infeccion Previa') {
                            $texto = 'Infección Previa';
                        } elseif ($sitio === '52. No cumple criterios') {
                            $texto = 'No Cumple Criterios';
                        } else {
                            $texto = 'No aplica';
                        }
                    } elseif ($clasificacion == '5') {
                        $texto = 'Infección sin Mios';
                    } elseif ($clasificacion == '6') {
                        $texto = 'Infección Polimicrobiano';
                    } elseif ($clasificacion == '7') {
                        $texto = 'Complicación';
                    }
                } elseif ($tipo == '2') {
                    if ($clasificacion == '1') {
                        $texto = 'Colonización Extrahospitalaria';
                    } elseif ($clasificacion == '2') {
                        $texto = 'Contaminado Extrahospitalaria';
                    } elseif ($clasificacion == '3') {
                        $texto = 'Infección Extrahospitalaria';
                    }
                }
                $validated['clasificacion_texto'] = $texto;
            }
        }

        // Control de edición: un usuario básico solo puede llenar una vez.
        $esAdmin = optional($request->user())->esAdmin();

        // ── Campos de la muestra ───────────────────────────────────────────
        // El formulario agrupa en un solo bloque todos los registros que
        // comparten el mismo microorganismo, así que estos campos llegan como
        // registros[<id>][campo] (un juego por cada registro duplicado).
        $filasMuestra = [];
        foreach ((array) $request->input('registros', []) as $idFila => $datosFila) {
            if (!is_array($datosFila)) {
                continue;
            }
            $datosFila = array_intersect_key($datosFila, array_flip(self::CAMPOS_MUESTRA));
            foreach ($datosFila as $campo => $valor) {
                $datosFila[$campo] = ($valor === '' ? null : $valor);
            }
            $filasMuestra[(int) $idFila] = $datosFila;
        }

        if ($request->filled('id')) {
            $registro = EpidemiologiaRegistro::findOrFail($request->id);

            if (!$esAdmin && $registro->edicion_bloqueada) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este formulario ya fue registrado. Solo un administrador puede modificarlo.',
                ], 403);
            }

            if ($request->has('fecha_quirurgica_previa')) {
                $fechaQxPrevia = $request->input('fecha_quirurgica_previa');
                if (!$fechaQxPrevia) {
                    $validated['dias_entre_qx_e_infeccion'] = null;
                } else {
                    // La fecha de referencia es la del registro principal del bloque.
                    if (array_key_exists('fecha_toma_muestra', $filasMuestra[$registro->id] ?? [])) {
                        $fechaTomaMuestra = $filasMuestra[$registro->id]['fecha_toma_muestra'];
                    } elseif ($request->filled('fecha_toma_muestra')) {
                        $fechaTomaMuestra = $request->input('fecha_toma_muestra');
                    } else {
                        $fechaTomaMuestra = $registro->fecha_toma_muestra ?? null;
                    }

                    if ($fechaTomaMuestra) {
                        $fechaMuestraStr = is_string($fechaTomaMuestra) ? $fechaTomaMuestra : $fechaTomaMuestra->format('Y-m-d');
                        $validated['dias_entre_qx_e_infeccion'] = $this->diasEntreQxEInfeccion($fechaMuestraStr, $fechaQxPrevia);
                    }
                }
            }

            $registro->update($validated);
        } else {
            if ($request->filled('fecha_quirurgica_previa') && $request->filled('fecha_toma_muestra')) {
                $validated['dias_entre_qx_e_infeccion'] = $this->diasEntreQxEInfeccion(
                    $request->input('fecha_toma_muestra'),
                    $request->input('fecha_quirurgica_previa')
                );
            }
            $registro = EpidemiologiaRegistro::create($validated);
        }

        // Si lo guardó un usuario básico, se bloquea para futuras ediciones suyas.
        if (!$esAdmin) {
            $registro->update(['edicion_bloqueada' => true]);
        }

        // ── Registros duplicados del mismo microorganismo ──────────────────
        // Cada sección "Registro #n" del bloque guarda sus propios campos de
        // muestra; los datos complementarios se llenan una vez y se replican
        // en todas las filas del bloque.
        if ($filasMuestra) {
            $complementarios = array_diff_key($validated, array_flip(self::CAMPOS_MUESTRA));
            unset($complementarios['dias_entre_qx_e_infeccion']); // se recalcula por fila

            $fechaQxBloque = $validated['fecha_quirurgica_previa'] ?? null;
            $recalcularDias = $request->has('fecha_quirurgica_previa');

            $consulta = EpidemiologiaRegistro::whereIn('id', array_keys($filasMuestra));
            if ($registro->paciente_id) {
                $consulta->where('paciente_id', $registro->paciente_id);
            }

            foreach ($consulta->get() as $fila) {
                $esPrincipal = (int) $fila->id === (int) $registro->id;

                if (!$esAdmin && !$esPrincipal && $fila->edicion_bloqueada) {
                    continue;
                }

                $datos = $filasMuestra[$fila->id] ?? [];

                if (!$esPrincipal && $complementarios) {
                    $datos += $complementarios;
                }

                if ($recalcularDias) {
                    $fechaMuestraFila = array_key_exists('fecha_toma_muestra', $datos)
                        ? $datos['fecha_toma_muestra']
                        : optional($fila->fecha_toma_muestra)->format('Y-m-d');

                    $datos['dias_entre_qx_e_infeccion'] = $this->diasEntreQxEInfeccion(
                        $fechaMuestraFila ? (string) $fechaMuestraFila : null,
                        $fechaQxBloque ? (string) $fechaQxBloque : null
                    );
                }

                if (!$esAdmin) {
                    $datos['edicion_bloqueada'] = true;
                }

                if ($datos) {
                    $fila->update($datos);
                }
            }
        }

        // ── Datos "constantes" del paciente ────────────────────────────────
        // Estos campos son iguales para todos los microorganismos del paciente.
        // Al guardar cualquier bloque, se replican en TODAS las filas del mismo
        // paciente para mantenerlos consistentes con un solo "Registrar".
        $camposConstantes = ['tipo_id', 'pais_origen', 'departamento', 'municipio', 'diagnostico_ingreso', 'asegurador', 'peso', 'fecha_ingreso_hosp'];
        $valoresConstantes = [];
        foreach ($camposConstantes as $campo) {
            if ($request->has($campo)) {
                $valoresConstantes[$campo] = $validated[$campo] ?? null;
            }
        }
        if (!empty($valoresConstantes) && $registro->paciente_id) {
            EpidemiologiaRegistro::where('paciente_id', $registro->paciente_id)
                ->where('id', '!=', $registro->id)
                ->update($valoresConstantes);
        }

        // Trazabilidad: registrar la actividad del usuario
        $esActualizacion = $request->filled('id');
        Actividad::registrar([
            'tipo'             => 'epidemiologia',
            'accion'           => $esActualizacion ? 'actualizar' : 'crear',
            'descripcion'      => ($esActualizacion ? 'Actualizó' : 'Registró') . ' datos de epidemiología'
                                  . ($registro->microorganismo ? ' — ' . $registro->microorganismo : ''),
            'referencia_tabla' => 'seguimiento_microbiologico',
            'referencia_id'    => $registro->id,
            'paciente'         => $registro->nombre ?: optional($registro->paciente)->nombre,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro guardado correctamente.',
            'id'      => $registro->id,
        ]);
    }

    /**
     * Días entre la cirugía previa y la infección.
     * Fórmula: (fecha de toma de muestra) - (fecha quirúrgica previa), en días.
     * Puede ser negativo si la muestra es anterior a la cirugía.
     */
    private function diasEntreQxEInfeccion(?string $fechaMuestra, ?string $fechaQx): ?int
    {
        if (!$fechaMuestra || !$fechaQx) {
            return null;
        }
        try {
            $m = (new \DateTime($fechaMuestra))->setTime(0, 0, 0);
            $q = (new \DateTime($fechaQx))->setTime(0, 0, 0);
            return (int) round(($m->getTimestamp() - $q->getTimestamp()) / 86400);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Vacía por completo la base de epidemiología: seguimientos y pacientes.
     * Acción destructiva e irreversible; solo administradores (ver rutas).
     */
    public function vaciar()
    {
        try {
            $seguimientos = EpidemiologiaRegistro::count();
            $pacientes    = Paciente::count();

            DB::transaction(function () {
                // Primero los seguimientos (referencian al paciente), luego los pacientes.
                EpidemiologiaRegistro::query()->delete();
                Paciente::query()->delete();
            });

            Actividad::registrar([
                'tipo'        => 'importacion',
                'accion'      => 'vaciar',
                'descripcion' => "Vació la base de epidemiología: {$seguimientos} seguimiento(s) y {$pacientes} paciente(s) eliminados.",
            ]);

            return redirect()->back()->with(
                'success',
                "Base de epidemiología vaciada: se eliminaron {$seguimientos} seguimiento(s) y {$pacientes} paciente(s)."
            );
        } catch (\Throwable $e) {
            Log::error('Error vaciando epidemiología: ' . $e->getMessage());

            return redirect()->back()->with('error', 'No se pudo vaciar la base de epidemiología: ' . $e->getMessage());
        }
    }

    /**
     * Obtener un registro por su ID.
     */
    public function obtener($id)
    {
        $registro = EpidemiologiaRegistro::findOrFail($id);
        return response()->json(['success' => true, 'data' => $registro]);
    }

    /**
     * Listado de todos los registros de seguimiento microbiológico.
     */
    public function index(Request $request)
    {
        $registros = EpidemiologiaRegistro::when($request->search, function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->search}%")
                  ->orWhere('id_historia', 'like', "%{$request->search}%")
                  ->orWhere('identificador_unico', 'like', "%{$request->search}%")
                  ->orWhere('microorganismo', 'like', "%{$request->search}%");
            })
            ->when($request->filled('con_proa'), fn($q) => $q->where('tiene_procedimiento', true))
            ->when($request->filled('cerrados'), fn($q) => $q->where('caso_cerrado', true))
            ->orderByDesc('fecha_toma_muestra')
            ->paginate(25);

        return view('epidemiologia.index', compact('registros'));
    }
}
