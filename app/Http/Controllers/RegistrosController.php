<?php

namespace App\Http\Controllers;

use App\Models\CategoriaQuirurgica;
use App\Models\DiagInfeccioso;
use App\Models\Diagnostico;
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
use App\Support\AlertasProa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
        // Revisa tratamientos que superan los 7 días y genera notificaciones,
        // a lo sumo una vez cada 12 horas (así no depende de un cron).
        if (!Cache::has('proa_alertas_revisadas')) {
            Cache::put('proa_alertas_revisadas', true, now()->addHours(12));
            try {
                AlertasProa::revisarTratamientosLargos();
            } catch (\Throwable $e) {
                // No debe romper la carga de la página.
            }
        }

        $search = trim((string) $request->get('search', ''));
        $servicioSeleccionado = $request->get('servicio');
        $anio = $request->get('anio');
        $mes  = $request->get('mes');
        // Filtro de tipo: 'todos' | 'proa' (con PROA) | 'epidemiologia' (solo epi)
        $tipo = $request->get('tipo', 'todos');

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

        // ── Conteo de PACIENTES por servicio (para las tarjetas) ───────────
        // Consulta directa: cuenta pacientes distintos por servicio con los
        // mismos filtros. Es la cifra correcta y evita depender de cargar todos
        // los registros solo para contar.
        $pacientesCountPorServicio = EpidemiologiaRegistro::query()
            ->whereIn('ubicacion', $serviciosNombres)
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
            ->selectRaw('ubicacion, COUNT(DISTINCT paciente_id) as total')
            ->groupBy('ubicacion')
            ->pluck('total', 'ubicacion');

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

        // El detalle completo (seguimientos, cruce PROA, semáforo) solo se
        // necesita al abrir un servicio. En la grilla de servicios basta con
        // los conteos calculados arriba, mucho más liviano.
        $dataPorServicio = collect();
        $intervenciones  = collect();

        if ($servicioSeleccionado) {

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

        // ── Intervenciones PROA existentes (se usan para el semáforo y el form) ─
        $intervenciones = IntervencionProa::query()
            ->whereIn('id_deta_procedimiento', $todosIds)
            ->get()
            ->keyBy('id_deta_procedimiento');
        // Ids de dosis que YA tienen intervención registrada.
        $idsConIntervencion = $intervenciones->keys()->flip();

        // Estructura final por servicio
        $dataPorServicio = $pacientesPorServicio->map(function ($listaPacientes) use ($procedimientosPorIdent, $procedimientosPorHist, $idsConIntervencion) {
            return $listaPacientes->map(function ($registrosPaciente) use ($procedimientosPorIdent, $procedimientosPorHist, $idsConIntervencion) {
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
                // Agrupar por antibiótico en MAYÚSCULA para que un mismo fármaco
                // no aparezca en varios bloques por diferencias de capitalización.
                $medicamentos = $matches->isNotEmpty()
                    ? $matches->sortBy('Fec_Sumistro')
                              ->groupBy(fn ($p) => mb_strtoupper(trim((string) $p->Antimicrobiano), 'UTF-8'))
                    : null;

                // ── Semaforización ─────────────────────────────────────────
                // Epidemiología: verde si TODAS las muestras están registradas.
                $epiCompleto = $registrosPaciente->every(fn ($s) => (bool) $s->registrado);
                // PROA: verde si las dosis MOSTRADAS (la primera de cada fecha,
                // por medicamento) tienen intervención registrada.
                $proaCompleto = $medicamentos && $medicamentos->isNotEmpty()
                    && $medicamentos->every(function ($dosisMed) use ($idsConIntervencion) {
                        return $dosisMed->groupBy(function ($p) {
                                return $p->Fec_Sumistro
                                    ? \Carbon\Carbon::parse($p->Fec_Sumistro)->format('Y-m-d')
                                    : 'sin-fecha';
                            })
                            ->map(fn ($g) => $g->first())
                            ->every(fn ($d) => $idsConIntervencion->has($d->id));
                    });

                return [
                    'paciente'      => $rep->paciente,
                    'seguimiento'   => $rep,                          // back-compat (cabecera / PROA)
                    'seguimientos'  => $registrosPaciente->values(),  // TODOS los microorganismos del paciente
                    'tiene_proa'    => $matches->isNotEmpty(),
                    'medicamentos'  => $medicamentos,
                    'total_medic'   => $medicamentos ? $medicamentos->count() : 0,
                    'total_dosis'   => $matches->count(),
                    'epi_completo'  => $epiCompleto,
                    'proa_completo' => $proaCompleto,
                ];
            });
        });

        // ── Filtro por tipo: con PROA / solo epidemiología / todos ─────────
        if (in_array($tipo, ['proa', 'epidemiologia'], true)) {
            $dataPorServicio = $dataPorServicio->map(function ($listaPacientes) use ($tipo) {
                return $listaPacientes->filter(function ($info) use ($tipo) {
                    return $tipo === 'proa' ? $info['tiene_proa'] : ! $info['tiene_proa'];
                });
            });
        }

        } // fin if ($servicioSeleccionado)

        // ── 5. Catálogos para formularios ─────────────────────────────────
        $catalogos = [
            'sisInternacional'    => SisInternacional::orderBy('descripcion')->get(),
            'frecuencias'         => Frecuencia::orderBy('descripcion')->get(),
            'perfiles'            => Perfil::orderBy('descripcion')->get(),
            'espTratantes'        => EspTratante::orderBy('descripcion')->get(),
            'diagInfecciosos'     => DiagInfeccioso::orderBy('descripcion')->get(),
            'tiposMuestra'        => TipMuestra::orderBy('id')->get(),
            'resultados'          => Resultado::orderBy('descripcion')->get(),
            'microorganismos'     => Microorganismo::orderBy('descripcion')->get(),
            'pantimicrobianos'    => Pantimicrobiano::orderBy('descripcion')->get(),
            'indicacionesTerapia' => IndicacionTerapia::orderBy('descripcion')->get(),
            'tratamientos'        => Tratamiento::orderBy('descripcion')->get(),
            'paises'              => Pais::orderBy('nombre')->get(),
            'categoriasQuirurgicas' => CategoriaQuirurgica::orderBy('descripcion')->get(),
            // Catálogo CIE-10 para el buscador de diagnóstico (solo en la vista de
            // pacientes, que es donde se muestran los formularios).
            'diagnosticos'        => $servicioSeleccionado
                ? Diagnostico::orderBy('codigo')->get(['codigo', 'descripcion'])
                : collect(),
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
            'tipo'                  => $tipo,
            'aniosDisponibles'      => $aniosDisponibles,
            'proaCountPorServicio'  => $proaCountPorServicio,
            'pacientesCountPorServicio' => $pacientesCountPorServicio,
            'pacientesPaginados'    => $pacientesPaginados,
        ]);
    }
}
