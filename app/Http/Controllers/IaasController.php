<?php

namespace App\Http\Controllers;

use App\Models\EpidemiologiaRegistro;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class IaasController extends Controller
{
    /**
     * IAAS = infección intrahospitalaria: tipo "1" (intrahospitalaria) y
     * clasificación 3 (Infección), 5 (Infección sin Mios) o 6 (Infección
     * Polimicrobiano). Se excluyen colonizaciones, contaminados, infecciones
     * extrahospitalarias, previas y "No aplica".
     */
    private const IAAS_TIPO = '1';
    private const IAAS_CLASIFICACIONES = ['3', '5', '6'];

    public function conteo(Request $request)
    {
        // Rango por defecto: lo corrido del mes actual. Personalizable por el usuario.
        $hoy = Carbon::today();
        $inicio = $request->filled('fecha_inicial')
            ? Carbon::parse($request->input('fecha_inicial'))->startOfDay()
            : $hoy->copy()->startOfMonth();
        $fin = $request->filled('fecha_final')
            ? Carbon::parse($request->input('fecha_final'))->endOfDay()
            : $hoy->copy()->endOfDay();

        // Si el usuario invierte el rango, se corrige.
        if ($inicio->gt($fin)) {
            [$inicio, $fin] = [$fin->copy()->startOfDay(), $inicio->copy()->endOfDay()];
        }

        $base = EpidemiologiaRegistro::whereBetween('fecha_toma_muestra', [$inicio, $fin]);

        // Consulta de IAAS (clona para no romper la base).
        $iaasQuery = (clone $base)
            ->where('tipo', self::IAAS_TIPO)
            ->whereIn('clasificacion', self::IAAS_CLASIFICACIONES);

        $totalIaas = (clone $iaasQuery)->count();
        $pacientesIaas = (clone $iaasQuery)->distinct('paciente_id')->count('paciente_id');

        // Desglose por servicio (solo IAAS).
        $porServicio = (clone $iaasQuery)
            ->selectRaw('ubicacion, COUNT(*) AS total, COUNT(DISTINCT paciente_id) AS pacientes')
            ->groupBy('ubicacion')
            ->orderByDesc('total')
            ->get();

        // Desglose por clasificación (TODOS los registros del rango, para transparencia).
        // Se agrupa por la columna real (compatible con ONLY_FULL_GROUP_BY) y el
        // reetiquetado de nulos/vacíos se hace en PHP.
        $porClasificacion = (clone $base)
            ->selectRaw('clasificacion_texto, COUNT(*) AS total')
            ->groupBy('clasificacion_texto')
            ->get()
            ->groupBy(fn ($r) => filled($r->clasificacion_texto) ? $r->clasificacion_texto : '(sin clasificar)')
            ->map(fn ($grp, $label) => (object) ['clasificacion' => $label, 'total' => $grp->sum('total')])
            ->sortByDesc('total')
            ->values();

        $totalRegistros = (clone $base)->count();

        return view('iaas.conteo', [
            'inicio'           => $inicio,
            'fin'              => $fin,
            'totalIaas'        => $totalIaas,
            'pacientesIaas'    => $pacientesIaas,
            'porServicio'      => $porServicio,
            'porClasificacion' => $porClasificacion,
            'totalRegistros'   => $totalRegistros,
        ]);
    }
}
