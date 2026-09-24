<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Catálogo de servicios (ubicaciones estandarizadas del hospital).
 */
class ServicioController extends Controller
{
    public function index(Request $request): View
    {
        $servicios = Servicio::query()
            // El valor se registra como equivalencia de si mismo; esa no cuenta
            // como "variante que lo alimenta".
            ->withCount(['equivalencias' => fn ($q) => $q->whereColumn('texto_crudo', '<>', 'servicios.nombre')])
            ->when($request->filled('q'), fn ($q) => $q->search($request->input('q')))
            ->orderBy('nombre')
            ->paginate(25)
            ->withQueryString();

        return view('servicios.index', compact('servicios'));
    }

    public function create(): View
    {
        return view('servicios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Servicio::rules(), Servicio::messages());

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            Servicio::create($request->only('nombre'));

            return redirect()->route('servicios.index')->with('success', 'Servicio creado correctamente.');
        } catch (Exception $e) {
            Log::error('Error en ServicioController@store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear el servicio: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Servicio $servicio): View
    {
        $servicio->load('equivalencias');

        return view('servicios.show', compact('servicio'));
    }

    public function edit(Servicio $servicio): View
    {
        return view('servicios.edit', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Servicio::rules($servicio->id), Servicio::messages());

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $anterior = $servicio->nombre;
            $servicio->update($request->only('nombre'));

            // Si cambió el nombre, las equivalencias que apuntaban al nombre
            // anterior quedarían huérfanas: se reapuntan al nuevo.
            if ($anterior !== $servicio->nombre) {
                \App\Models\Equivalencia::where('catalogo', \App\Models\Equivalencia::CATALOGO_SERVICIO)
                    ->where('valor', $anterior)
                    ->update(['valor' => $servicio->nombre]);

                \App\Support\Estandarizador::olvidarCache();
            }

            return redirect()->route('servicios.index')->with('success', 'Servicio actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error en ServicioController@update: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el servicio: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Servicio $servicio): JsonResponse|RedirectResponse
    {
        try {
            $enUso = $servicio->equivalencias()->count();

            if ($enUso > 0) {
                $mensaje = "No se puede eliminar: {$enUso} equivalencia(s) apuntan a este servicio.";

                return request()->ajax()
                    ? response()->json(['success' => false, 'message' => $mensaje], 422)
                    : redirect()->route('servicios.index')->with('error', $mensaje);
            }

            $servicio->delete();

            return request()->ajax()
                ? response()->json(['success' => true, 'message' => 'Servicio eliminado correctamente.'])
                : redirect()->route('servicios.index')->with('success', 'Servicio eliminado correctamente.');
        } catch (Exception $e) {
            Log::error('Error en ServicioController@destroy: ' . $e->getMessage());

            return request()->ajax()
                ? response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500)
                : redirect()->route('servicios.index')->with('error', 'Error al eliminar el servicio: ' . $e->getMessage());
        }
    }
}
