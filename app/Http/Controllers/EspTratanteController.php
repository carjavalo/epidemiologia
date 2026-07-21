<?php

namespace App\Http\Controllers;

use App\Models\EspTratante;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EspTratanteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            if ($request->ajax()) {
                $espTratantes = EspTratante::query();

                // Aplicar búsqueda si existe
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $espTratantes->search($searchValue);
                }

                // Aplicar ordenamiento
                if ($request->has('order')) {
                    $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                    $orderDirection = $request->order[0]['dir'];
                    $espTratantes->orderBy($orderColumn, $orderDirection);
                } else {
                    $espTratantes->orderBy('created_at', 'desc');
                }

                // Obtener total de registros
                $totalRecords = EspTratante::count();
                $filteredRecords = $espTratantes->count();

                // Aplicar paginación
                $start = $request->get('start', 0);
                $length = $request->get('length', 10);
                $espTratantes = $espTratantes->skip($start)->take($length)->get();

                // Formatear datos para DataTable
                $data = $espTratantes->map(function ($espTratante) {
                    return [
                        'id' => $espTratante->id,
                        'descripcion' => $espTratante->descripcion,
                        'created_at' => $espTratante->created_at->format('d/m/Y H:i'),
                        'updated_at' => $espTratante->updated_at->format('d/m/Y H:i'),
                        'actions' => view('esp-tratante.partials.actions', compact('espTratante'))->render(),
                    ];
                });

                return response()->json([
                    'draw' => intval($request->get('draw')),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data' => $data,
                ]);
            }

            return view('esp-tratante.index');
        } catch (Exception $e) {
            Log::error('Error en EspTratanteController@index: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error al cargar las especialidades tratantes: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar las especialidades tratantes: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            return view('esp-tratante.create');
        } catch (Exception $e) {
            Log::error('Error en EspTratanteController@create: ' . $e->getMessage());
            return redirect()->route('esp-tratante.index')
                ->with('error', 'Error al cargar el formulario de creación: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), EspTratante::rules(), EspTratante::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            EspTratante::create($request->only(['descripcion']));

            return redirect()->route('esp-tratante.index')
                ->with('success', 'Especialidad Tratante creada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en EspTratanteController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear la especialidad tratante: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(EspTratante $espTratante): View|RedirectResponse
    {
        try {
            return view('esp-tratante.show', compact('espTratante'));
        } catch (Exception $e) {
            Log::error('Error en EspTratanteController@show: ' . $e->getMessage());
            return redirect()->route('esp-tratante.index')
                ->with('error', 'Error al mostrar la especialidad tratante: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EspTratante $espTratante): View|RedirectResponse
    {
        try {
            return view('esp-tratante.edit', compact('espTratante'));
        } catch (Exception $e) {
            Log::error('Error en EspTratanteController@edit: ' . $e->getMessage());
            return redirect()->route('esp-tratante.index')
                ->with('error', 'Error al cargar el formulario de edición: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EspTratante $espTratante): RedirectResponse
    {
        try {
            $rules = EspTratante::rules();
            // Excluir el registro actual de la validación de unicidad
            $rules['descripcion'][] = 'unique:esp_tratante,descripcion,' . $espTratante->id;

            $validator = Validator::make($request->all(), $rules, EspTratante::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $espTratante->update($request->only(['descripcion']));

            return redirect()->route('esp-tratante.index')
                ->with('success', 'Especialidad Tratante actualizada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en EspTratanteController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar la especialidad tratante: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EspTratante $espTratante): JsonResponse|RedirectResponse
    {
        try {
            $descripcion = $espTratante->descripcion;
            $espTratante->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Especialidad Tratante '{$descripcion}' eliminada exitosamente."
                ]);
            }

            return redirect()->route('esp-tratante.index')
                ->with('success', "Especialidad Tratante '{$descripcion}' eliminada exitosamente.");
        } catch (Exception $e) {
            Log::error('Error en EspTratanteController@destroy: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la especialidad tratante: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('esp-tratante.index')
                ->with('error', 'Error al eliminar la especialidad tratante: ' . $e->getMessage());
        }
    }
}
