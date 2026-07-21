<?php

namespace App\Http\Controllers;

use App\Models\PlantillaObservacion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class PlantillaObservacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            if ($request->ajax()) {
                try {
                    $plantillas = PlantillaObservacion::select(['id', 'descripcion', 'created_at', 'updated_at']);

                    if ($request->has('search') && !empty($request->search['value'])) {
                        $searchValue = $request->search['value'];
                        $plantillas->where('descripcion', 'like', '%' . $searchValue . '%');
                    }

                    $totalRecords = PlantillaObservacion::count();
                    $filteredRecords = $plantillas->count();

                    if ($request->has('order')) {
                        $orderColumn = $request->order[0]['column'];
                        $orderDirection = $request->order[0]['dir'];
                        $columns = ['id', 'descripcion', 'created_at', 'updated_at'];
                        if (isset($columns[$orderColumn])) {
                            $plantillas->orderBy($columns[$orderColumn], $orderDirection);
                        }
                    } else {
                        $plantillas->orderBy('id', 'desc');
                    }

                    if ($request->has('start') && $request->has('length')) {
                        $plantillas->skip($request->start)->take($request->length);
                    }

                    $data = $plantillas->get()->map(function ($plantilla) {
                        return [
                            'id' => $plantilla->id,
                            'descripcion' => $plantilla->descripcion,
                            'created_at' => $plantilla->created_at ? $plantilla->created_at->format('d/m/Y H:i') : '',
                            'updated_at' => $plantilla->updated_at ? $plantilla->updated_at->format('d/m/Y H:i') : '',
                            'actions' => view('plantillas-observaciones.partials.actions', compact('plantilla'))->render()
                        ];
                    });

                    Log::info('Processing AJAX request for plantillas-observaciones');

                    return response()->json([
                        'draw' => intval($request->draw),
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $filteredRecords,
                        'data' => $data
                    ]);
                } catch (Exception $e) {
                    Log::error('Error en AJAX PlantillaObservacionController@index: ' . $e->getMessage());
                    return response()->json(['error' => 'Error al procesar la solicitud AJAX'], 500);
                }
            }

            Log::info('PlantillaObservacionController@index called', [
                'is_ajax' => $request->ajax(),
                'headers' => $request->headers->all(),
                'method' => $request->method()
            ]);

            return view('plantillas-observaciones.index');
        } catch (Exception $e) {
            Log::error('Error en PlantillaObservacionController@index: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'Error al cargar las plantillas de observaciones'], 500);
            }
            return redirect()->back()->with('error', 'Error al cargar las plantillas de observaciones: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            return view('plantillas-observaciones.create');
        } catch (Exception $e) {
            Log::error('Error en PlantillaObservacionController@create: ' . $e->getMessage());
            return redirect()->route('plantillas-observaciones.index')->with('error', 'Error al mostrar el formulario de creación: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), PlantillaObservacion::rules(), PlantillaObservacion::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            PlantillaObservacion::create($request->only(['descripcion']));

            return redirect()->route('plantillas-observaciones.index')
                ->with('success', 'Plantilla de observación creada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en PlantillaObservacionController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear la plantilla de observación: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PlantillaObservacion $plantillaObservacion): View|RedirectResponse
    {
        try {
            // Verificar que la plantilla existe
            if (!$plantillaObservacion->exists) {
                return redirect()->route('plantillas-observaciones.index')
                    ->with('error', 'La plantilla de observación solicitada no existe.');
            }

            return view('plantillas-observaciones.show', compact('plantillaObservacion'));
        } catch (Exception $e) {
            Log::error('Error en PlantillaObservacionController@show: ' . $e->getMessage());
            return redirect()->route('plantillas-observaciones.index')->with('error', 'Error al mostrar la plantilla de observación: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlantillaObservacion $plantillaObservacion): View|RedirectResponse
    {
        try {
            // Verificar que la plantilla existe
            if (!$plantillaObservacion->exists) {
                return redirect()->route('plantillas-observaciones.index')
                    ->with('error', 'La plantilla de observación solicitada no existe.');
            }

            return view('plantillas-observaciones.edit', compact('plantillaObservacion'));
        } catch (Exception $e) {
            Log::error('Error en PlantillaObservacionController@edit: ' . $e->getMessage());
            return redirect()->route('plantillas-observaciones.index')->with('error', 'Error al mostrar el formulario de edición: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PlantillaObservacion $plantillaObservacion): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), PlantillaObservacion::rules(), PlantillaObservacion::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $plantillaObservacion->update($request->only(['descripcion']));

            return redirect()->route('plantillas-observaciones.index')
                ->with('success', 'Plantilla de observación actualizada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en PlantillaObservacionController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar la plantilla de observación: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlantillaObservacion $plantillaObservacion): RedirectResponse
    {
        try {
            $plantillaObservacion->delete();

            return redirect()->route('plantillas-observaciones.index')
                ->with('success', 'Plantilla de observación eliminada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en PlantillaObservacionController@destroy: ' . $e->getMessage());
            return redirect()->route('plantillas-observaciones.index')
                ->with('error', 'Error al eliminar la plantilla de observación: ' . $e->getMessage());
        }
    }
}
