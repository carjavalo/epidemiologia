<?php

namespace App\Http\Controllers;

use App\Models\Frecuencia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class FrecuenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            if ($request->ajax()) {
                $frecuencias = Frecuencia::query();

                // Aplicar búsqueda si existe
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $frecuencias->search($searchValue);
                }

                // Aplicar ordenamiento
                if ($request->has('order')) {
                    $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                    $orderDirection = $request->order[0]['dir'];
                    $frecuencias->orderBy($orderColumn, $orderDirection);
                } else {
                    $frecuencias->orderBy('created_at', 'desc');
                }

                $totalRecords = Frecuencia::count();
                $filteredRecords = $frecuencias->count();

                // Aplicar paginación
                if ($request->has('start') && $request->has('length')) {
                    $frecuencias->skip($request->start)->take($request->length);
                }

                $data = $frecuencias->get()->map(function ($frecuencia) {
                    return [
                        'id' => $frecuencia->id,
                        'descripcion' => $frecuencia->descripcion,
                        'created_at' => $frecuencia->created_at_formatted,
                        'updated_at' => $frecuencia->updated_at_formatted,
                        'actions' => view('frecuencia.partials.actions', compact('frecuencia'))->render()
                    ];
                });

                return response()->json([
                    'draw' => intval($request->draw),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data' => $data
                ]);
            }

            return view('frecuencia.index');
        } catch (Exception $e) {
            Log::error('Error en FrecuenciaController@index: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => 'Error al cargar los datos'], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar las frecuencias.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('frecuencia.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), Frecuencia::rules(), Frecuencia::messages());

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $frecuencia = Frecuencia::create($request->only(['descripcion']));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Frecuencia creada exitosamente.',
                    'data' => $frecuencia
                ]);
            }

            return redirect()->route('frecuencia.index')
                ->with('success', 'Frecuencia creada exitosamente.');

        } catch (Exception $e) {
            Log::error('Error en FrecuenciaController@store: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la frecuencia.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al crear la frecuencia.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Frecuencia $frecuencia): View
    {
        return view('frecuencia.show', compact('frecuencia'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Frecuencia $frecuencia): View
    {
        return view('frecuencia.edit', compact('frecuencia'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Frecuencia $frecuencia): RedirectResponse|JsonResponse
    {
        try {
            $validator = Validator::make(
                $request->all(),
                Frecuencia::rules($frecuencia->id),
                Frecuencia::messages()
            );

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $frecuencia->update($request->only(['descripcion']));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Frecuencia actualizada exitosamente.',
                    'data' => $frecuencia
                ]);
            }

            return redirect()->route('frecuencia.index')
                ->with('success', 'Frecuencia actualizada exitosamente.');

        } catch (Exception $e) {
            Log::error('Error en FrecuenciaController@update: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la frecuencia.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al actualizar la frecuencia.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Frecuencia $frecuencia): JsonResponse
    {
        try {
            $descripcion = $frecuencia->descripcion;
            $frecuencia->delete();

            return response()->json([
                'success' => true,
                'message' => "Frecuencia '{$descripcion}' eliminada exitosamente."
            ]);

        } catch (Exception $e) {
            Log::error('Error en FrecuenciaController@destroy: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la frecuencia.'
            ], 500);
        }
    }
}
