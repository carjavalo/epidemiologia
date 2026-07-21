<?php

namespace App\Http\Controllers;

use App\Models\CategoriaQuirurgica;
use App\Models\DiagInfeccioso;
use App\Models\EpidemiologiaRegistro;
use App\Models\EspTratante;
use App\Models\Frecuencia;
use App\Models\IndicacionTerapia;
use App\Models\IntervencionProa;
use App\Models\Microorganismo;
use App\Models\Pais;
use App\Models\Paciente;
use App\Models\Pantimicrobiano;
use App\Models\Perfil;
use App\Models\Procedimiento;
use App\Models\Resultado;
use App\Models\SisInternacional;
use App\Models\TipMuestra;
use App\Models\Tratamiento;
use Illuminate\Http\Request;

class RegistrosController extends Controller
{
    /**
     * Listado por servicio de la base de epidemiología (base madre).
     *
     * Flujo:
     *  1. Los SERVICIOS se obtienen de `seguimiento_microbiologico.ubicacion`.
     *  2. Los PACIENTES dentro de cada servicio se cargan desde la tabla `pacientes`.
     *  3. Por cada paciente:
     *       - El bloque "Datos básicos" se llena desde el seguimiento microbiológico (epidemiología).
     *       - El bloque PROA se carga SOLO si el paciente está en `deta_procedimientos`
     *         (cruce por Num_Ident == identificador_unico  o  Hist_Clinica == id_historia).
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $servicioSeleccionado = $request->get('servicio');
        $anio = $request->get('anio');
        $mes  = $request->get('mes');

        // ── 0. Años disponibles para el filtro (según fecha de toma de muestra) ─
        $aniosDisponibles = EpidemiologiaRegistro::query()
            ->selectRaw('DISTINCT YEAR(fecha_toma_muestra) as anio')
            ->whereNotNull('fecha_toma_muestra')
            ->orderByDesc('anio')
            ->pluck('anio')
            ->filter()
            ->values();

        // ── 1. Servicios disponibles (de epidemiología) ───────────────────
        $serviciosPaginados = EpidemiologiaRegistro::query()
            ->select('ubicacion')
            ->whereNotNull('ubicacion')
            ->where('ubicacion', '!=', '')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('ubicacion', 'like', "%{$search}%")
                      ->orWhere('nombre', 'like', "%{$search}%")
                      ->orWhere('identificador_unico', 'like', "%{$search}%")
                      ->orWhere('id_historia', 'like', "%{$search}%");
                });
            })
            ->when($anio, fn ($q) => $q->whereYear('fecha_toma_muestra', $anio))
            ->when($mes, fn ($q) => $q->whereMonth('fecha_toma_muestra', $mes))
            ->when($servicioSeleccionado, fn ($q) => $q->where('ubicacion', $servicioSeleccionado))
            ->groupBy('ubicacion')
            ->orderBy('ubicacion')
            ->paginate(12)
            ->withQueryString();

        $serviciosNombres = $serviciosPaginados->pluck('ubicacion');

        // ── PROA count por servicio (para píldora en las tarjetas) ─────────
        $proaCountPorServicio = EpidemiologiaRegistro::query()
            ->whereIn('ubicacion', $serviciosNombres)
            ->where('tiene_intervencion_proa', true)
            ->whereNotNull('paciente_id')
            ->selectRaw('ubicacion, COUNT(DISTINCT paciente_id) as total_proa')
            ->groupBy('ubicacion')
            ->pluck('total_proa', 'ubicacion');

        // ── 2a. Si hay servicio seleccionado: paginar pacientes ────────────
        $pacientesPaginados = null;
        $pacienteIdsEnPagina = collect();

        if ($servicioSeleccionado) {
            $pacientesPaginados = EpidemiologiaRegistro::query()
                ->select('paciente_id')
                ->where('ubicacion', $servicioSeleccionado)
                ->whereNotNull('paciente_id')
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($w) use ($search) {
                        $w->where('nombre', 'like', "%{$search}%")
                          ->orWhere('identificador_unico', 'like', "%{$search}%")
                          ->orWhere('id_historia', 'like', "%{$search}%");
                    });
                })
                ->when($anio, fn ($q) => $q->whereYear('fecha_toma_muestra', $anio))
                ->when($mes, fn ($q) => $q->whereMonth('fecha_toma_muestra', $mes))
                ->groupBy('paciente_id')
                ->paginate(20, ['paciente_id'], 'pag')
                ->withQueryString();

            $pacienteIdsEnPagina = $pacientesPaginados->pluck('paciente_id');
        }

        // ── 2b. Seguimientos (epidemiología) de esos servicios ─────────────
        $seguimientos = EpidemiologiaRegistro::query()
            ->whereIn('ubicacion', $serviciosNombres)
            ->whereNotNull('paciente_id')
            ->when($pacienteIdsEnPagina->isNotEmpty(), fn ($q) => $q->whereIn('paciente_id', $pacienteIdsEnPagina))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('nombre', 'like', "%{$search}%")
                      ->orWhere('identificador_unico', 'like', "%{$search}%")
                      ->orWhere('id_historia', 'like', "%{$search}%");
                });
            })
            ->when($anio, fn ($q) => $q->whereYear('fecha_toma_muestra', $anio))
            ->when($mes, fn ($q) => $q->whereMonth('fecha_toma_muestra', $mes))
            ->with('paciente')
            ->orderBy('ubicacion')
            ->orderByDesc('fecha_toma_muestra')
            ->get();

        // Agrupar TODOS los seguimientos por paciente dentro del servicio
        // (un paciente puede tener varios registros, uno por microorganismo)
        $pacientesPorServicio = $seguimientos
            ->groupBy('ubicacion')
            ->map(function ($listaServicio) {
                return $listaServicio
                    ->filter(fn ($s) => $s->paciente_id)
                    ->groupBy('paciente_id');
            });

        // ── 3. Cruce con PROA: agrupar por identificador / historia ────────
        $identificadores = $seguimientos->pluck('identificador_unico')->filter()->unique()->values();
        $historias        = $seguimientos->pluck('id_historia')->filter()->unique()->values();

        $procedimientosPorIdent = collect();
        $procedimientosPorHist  = collect();
        $todosIds               = collect();

        if ($identificadores->isNotEmpty() || $historias->isNotEmpty()) {
            $todosProc = Procedimiento::query()
                ->where(function ($q) use ($identificadores, $historias) {
                    if ($identificadores->isNotEmpty()) {
                        $q->whereIn('Num_Ident', $identificadores);
                    }
                    if ($historias->isNotEmpty()) {
                        $q->orWhereIn('Hist_Clinica', $historias);
                    }
                })
                ->get();

            $procedimientosPorIdent = $todosProc->groupBy('Num_Ident');
            $procedimientosPorHist  = $todosProc->groupBy('Hist_Clinica');
            $todosIds               = $todosProc->pluck('id');
        }

        // Estructura final por servicio
        $dataPorServicio = $pacientesPorServicio->map(function ($listaPacientes) use ($procedimientosPorIdent, $procedimientosPorHist) {
            return $listaPacientes->map(function ($registrosPaciente) use ($procedimientosPorIdent, $procedimientosPorHist) {
                // Representante para cabecera del paciente y cruce PROA
                // (identificador / historia son iguales en todos sus registros)
                $rep     = $registrosPaciente->first();
                $ident   = $rep->identificador_unico;
                $hist    = $rep->id_historia;
                $matches = collect();

                if ($ident && $procedimientosPorIdent->has($ident)) {
                    $matches = $matches->concat($procedimientosPorIdent->get($ident));
                }
                if ($hist && $procedimientosPorHist->has($hist)) {
                    $matches = $matches->concat($procedimientosPorHist->get($hist));
                }

                $matches = $matches->unique('id');
                $medicamentos = $matches->isNotEmpty() ? $matches->groupBy('Antimicrobiano') : null;

                return [
                    'paciente'     => $rep->paciente,
                    'seguimiento'  => $rep,                          // back-compat (cabecera / PROA)
                    'seguimientos' => $registrosPaciente->values(),  // TODOS los microorganismos del paciente
                    'tiene_proa'   => $matches->isNotEmpty(),
                    'medicamentos' => $medicamentos,
                    'total_medic'  => $medicamentos ? $medicamentos->count() : 0,
                    'total_dosis'  => $matches->count(),
                ];
            });
        });

        // ── 4. Intervenciones PROA existentes ──────────────────────────────
        $intervenciones = IntervencionProa::query()
            ->whereIn('id_deta_procedimiento', $todosIds)
            ->get()
            ->keyBy('id_deta_procedimiento');

        // ── 5. Catálogos para formularios ─────────────────────────────────
        $catalogos = [
            'sisInternacional'    => SisInternacional::orderBy('descripcion')->get(),
            'frecuencias'         => Frecuencia::orderBy('descripcion')->get(),
            'perfiles'            => Perfil::orderBy('descripcion')->get(),
            'espTratantes'        => EspTratante::orderBy('descripcion')->get(),
            'diagInfecciosos'     => DiagInfeccioso::orderBy('descripcion')->get(),
            'tiposMuestra'        => TipMuestra::orderBy('descripcion')->get(),
            'resultados'          => Resultado::orderBy('descripcion')->get(),
            'microorganismos'     => Microorganismo::orderBy('descripcion')->get(),
            'pantimicrobianos'    => Pantimicrobiano::orderBy('descripcion')->get(),
            'indicacionesTerapia' => IndicacionTerapia::orderBy('descripcion')->get(),
            'tratamientos'        => Tratamiento::orderBy('descripcion')->get(),
            'paises'              => Pais::orderBy('nombre')->get(),
            'categoriasQuirurgicas' => CategoriaQuirurgica::orderBy('descripcion')->get(),
        ];

        return view('registros.index', [
            'serviciosPaginados'    => $serviciosPaginados,
            'dataPorServicio'       => $dataPorServicio,
            'search'                => $search,
            'intervenciones'        => $intervenciones,
            'catalogos'             => $catalogos,
            'servicioSeleccionado'  => $servicioSeleccionado,
            'anio'                  => $anio,
            'mes'                   => $mes,
            'aniosDisponibles'      => $aniosDisponibles,
            'proaCountPorServicio'  => $proaCountPorServicio,
            'pacientesPaginados'    => $pacientesPaginados,
        ]);
    }
}
