<?php

namespace App\Http\Controllers;

use App\Models\Tratamiento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class TratamientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            if ($request->ajax()) {
                $tratamientos = Tratamiento::query();

                // Aplicar búsqueda si existe
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $tratamientos->search($searchValue);
                }

                // Aplicar ordenamiento
                if ($request->has('order')) {
                    $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                    $orderDirection = $request->order[0]['dir'];
                    $tratamientos->orderBy($orderColumn, $orderDirection);
                } else {
                    $tratamientos->orderBy('created_at', 'desc');
                }

                // Obtener total de registros
                $totalRecords = Tratamiento::count();
                $filteredRecords = $tratamientos->count();

                // Aplicar paginación
                $start = $request->get('start', 0);
                $length = $request->get('length', 10);
                $tratamientos = $tratamientos->skip($start)->take($length)->get();

                // Formatear datos para DataTable
                $data = $tratamientos->map(function ($tratamiento) {
                    return [
                        'id' => $tratamiento->id,
                        'descripcion' => $tratamiento->descripcion,
                        'created_at' => $tratamiento->created_at->format('d/m/Y H:i'),
                        'updated_at' => $tratamiento->updated_at->format('d/m/Y H:i'),
                        'actions' => view('tratamiento.partials.actions', compact('tratamiento'))->render(),
                    ];
                });

                return response()->json([
                    'draw' => intval($request->get('draw')),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data' => $data,
                ]);
            }

            return view('tratamiento.index');
        } catch (Exception $e) {
            Log::error('Error en TratamientoController@index: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error al cargar los datos: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar los tratamientos: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            return view('tratamiento.create');
        } catch (Exception $e) {
            Log::error('Error en TratamientoController@create: ' . $e->getMessage());
            return redirect()->route('tratamiento.index')
                ->with('error', 'Error al cargar el formulario de creación: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Tratamiento::rules(), Tratamiento::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            Tratamiento::create($request->only(['descripcion']));

            return redirect()->route('tratamiento.index')
                ->with('success', 'Tratamiento creado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en TratamientoController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear el tratamiento: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Tratamiento $tratamiento): View|RedirectResponse
    {
        try {
            return view('tratamiento.show', compact('tratamiento'));
        } catch (Exception $e) {
            Log::error('Error en TratamientoController@show: ' . $e->getMessage());
            return redirect()->route('tratamiento.index')
                ->with('error', 'Error al mostrar el tratamiento: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tratamiento $tratamiento): View|RedirectResponse
    {
        try {
            return view('tratamiento.edit', compact('tratamiento'));
        } catch (Exception $e) {
            Log::error('Error en TratamientoController@edit: ' . $e->getMessage());
            return redirect()->route('tratamiento.index')
                ->with('error', 'Error al cargar el formulario de edición: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tratamiento $tratamiento): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Tratamiento::rules($tratamiento->id), Tratamiento::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $tratamiento->update($request->only(['descripcion']));

            return redirect()->route('tratamiento.index')
                ->with('success', 'Tratamiento actualizado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en TratamientoController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar el tratamiento: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tratamiento $tratamiento): JsonResponse|RedirectResponse
    {
        try {
            $tratamiento->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tratamiento eliminado exitosamente.'
                ]);
            }

            return redirect()->route('tratamiento.index')
                ->with('success', 'Tratamiento eliminado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en TratamientoController@destroy: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el tratamiento: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('tratamiento.index')
                ->with('error', 'Error al eliminar el tratamiento: ' . $e->getMessage());
        }
    }
}
