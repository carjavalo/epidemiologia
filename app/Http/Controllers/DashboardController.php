<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal del sistema, incluyendo el
     * historial de actividades de los usuarios (trazabilidad).
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // El historial de actividades es exclusivo de administradores.
        $esAdmin = optional($request->user())->esAdmin();

        $filtroUser = $request->get('user_id');
        $filtroTipo = $request->get('tipo');

        $actividades = collect();
        $usuarios = collect();

        if ($esAdmin) {
            $actividades = Actividad::with('user')
                ->when($filtroUser, fn ($q) => $q->where('user_id', $filtroUser))
                ->when($filtroTipo, fn ($q) => $q->where('tipo', $filtroTipo))
                ->latest()
                ->limit(100)
                ->get();

            $usuarios = User::orderBy('usuario')->get();
        }

        return view('dashboard', [
            'actividades' => $actividades,
            'usuarios'    => $usuarios,
            'filtroUser'  => $filtroUser,
            'filtroTipo'  => $filtroTipo,
        ]);
    }
}
