<?php

namespace App\Http\Controllers;

use App\Models\Equivalencia;
use App\Models\Sitio;
use App\Support\Estandarizador;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Catálogo del campo SITIO del formulario de epidemiología.
 */
class SitioController extends Controller
{
    public function index(Request $request): View
    {
        $sitios = Sitio::query()
            // El valor se registra como equivalencia de si mismo; esa no cuenta
            // como "variante que lo alimenta".
            ->withCount(['equivalencias' => fn ($q) => $q->whereColumn('texto_crudo', '<>', 'sitios.nombre')])
            ->when($request->filled('q'), fn ($q) => $q->search($request->input('q')))
            ->ordenNatural()
            ->paginate(30)
            ->withQueryString();

        return view('sitios.index', compact('sitios'));
    }

    public function create(): View
    {
        return view('sitios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Sitio::rules(), Sitio::messages());

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            Sitio::create($request->only('nombre'));
            Estandarizador::olvidarCache(Estandarizador::SITIO);

            return redirect()->route('sitios.index')->with('success', 'Sitio creado correctamente.');
        } catch (Exception $e) {
            Log::error('Error en SitioController@store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear el sitio: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Sitio $sitio): View
    {
        $sitio->load('equivalencias');

        return view('sitios.show', compact('sitio'));
    }

    public function edit(Sitio $sitio): View
    {
        return view('sitios.edit', compact('sitio'));
    }

    public function update(Request $request, Sitio $sitio): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Sitio::rules($sitio->id), Sitio::messages());

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $anterior = $sitio->nombre;
            $sitio->update($request->only('nombre'));

            // Las equivalencias que apuntaban al nombre anterior se reapuntan.
            if ($anterior !== $sitio->nombre) {
                Equivalencia::where('catalogo', Equivalencia::CATALOGO_SITIO)
                    ->where('valor', $anterior)
                    ->update(['valor' => $sitio->nombre]);
            }

            Estandarizador::olvidarCache();

            return redirect()->route('sitios.index')->with('success', 'Sitio actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error en SitioController@update: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el sitio: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Sitio $sitio): JsonResponse|RedirectResponse
    {
        try {
            // La auto-equivalencia (el sitio como variante de sí mismo) no cuenta
            // como uso: es la que crea el propio sistema.
            $enUso = $sitio->equivalencias()->where('texto_crudo', '<>', $sitio->nombre)->count();

            if ($enUso > 0) {
                $mensaje = "No se puede eliminar: {$enUso} equivalencia(s) apuntan a este sitio.";

                return request()->ajax()
                    ? response()->json(['success' => false, 'message' => $mensaje], 422)
                    : redirect()->route('sitios.index')->with('error', $mensaje);
            }

            Equivalencia::where('catalogo', Equivalencia::CATALOGO_SITIO)
                ->where('valor', $sitio->nombre)
                ->delete();

            $sitio->delete();
            Estandarizador::olvidarCache();

            return request()->ajax()
                ? response()->json(['success' => true, 'message' => 'Sitio eliminado correctamente.'])
                : redirect()->route('sitios.index')->with('success', 'Sitio eliminado correctamente.');
        } catch (Exception $e) {
            Log::error('Error en SitioController@destroy: ' . $e->getMessage());

            return request()->ajax()
                ? response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500)
                : redirect()->route('sitios.index')->with('error', 'Error al eliminar el sitio: ' . $e->getMessage());
        }
    }
}
