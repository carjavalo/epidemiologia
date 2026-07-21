<?php

namespace App\Http\Controllers;

use App\Models\Resultado;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class ResultadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            if ($request->ajax()) {
                $resultados = Resultado::query();

                // Aplicar búsqueda si existe
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $resultados->search($searchValue);
                }

                // Aplicar ordenamiento
                if ($request->has('order')) {
                    $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                    $orderDirection = $request->order[0]['dir'];
                    $resultados->orderBy($orderColumn, $orderDirection);
                } else {
                    $resultados->orderBy('created_at', 'desc');
                }

                // Obtener total de registros
                $totalRecords = Resultado::count();
                $filteredRecords = $resultados->count();

                // Aplicar paginación
                $start = $request->get('start', 0);
                $length = $request->get('length', 10);
                $resultados = $resultados->skip($start)->take($length)->get();

                // Formatear datos para DataTable
                $data = $resultados->map(function ($resultado) {
                    return [
                        'id' => $resultado->id,
                        'descripcion' => $resultado->descripcion,
                        'created_at' => $resultado->created_at->format('d/m/Y H:i'),
                        'updated_at' => $resultado->updated_at->format('d/m/Y H:i'),
                        'actions' => view('resultado.partials.actions', compact('resultado'))->render(),
                    ];
                });

                return response()->json([
                    'draw' => intval($request->get('draw')),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data' => $data,
                ]);
            }

            return view('resultado.index');
        } catch (Exception $e) {
            Log::error('Error en ResultadoController@index: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error al cargar los datos: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar los resultados: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            return view('resultado.create');
        } catch (Exception $e) {
            Log::error('Error en ResultadoController@create: ' . $e->getMessage());
            return redirect()->route('resultado.index')
                ->with('error', 'Error al cargar el formulario de creación: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Resultado::rules(), Resultado::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            Resultado::create($request->only(['descripcion']));

            return redirect()->route('resultado.index')
                ->with('success', 'Resultado creado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en ResultadoController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear el resultado: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Resultado $resultado): View|RedirectResponse
    {
        try {
            return view('resultado.show', compact('resultado'));
        } catch (Exception $e) {
            Log::error('Error en ResultadoController@show: ' . $e->getMessage());
            return redirect()->route('resultado.index')
                ->with('error', 'Error al mostrar el resultado: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resultado $resultado): View|RedirectResponse
    {
        try {
            return view('resultado.edit', compact('resultado'));
        } catch (Exception $e) {
            Log::error('Error en ResultadoController@edit: ' . $e->getMessage());
            return redirect()->route('resultado.index')
                ->with('error', 'Error al cargar el formulario de edición: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resultado $resultado): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Resultado::rules($resultado->id), Resultado::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $resultado->update($request->only(['descripcion']));

            return redirect()->route('resultado.index')
                ->with('success', 'Resultado actualizado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en ResultadoController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar el resultado: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resultado $resultado): JsonResponse|RedirectResponse
    {
        try {
            $resultado->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Resultado eliminado exitosamente.'
                ]);
            }

            return redirect()->route('resultado.index')
                ->with('success', 'Resultado eliminado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en ResultadoController@destroy: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el resultado: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('resultado.index')
                ->with('error', 'Error al eliminar el resultado: ' . $e->getMessage());
        }
    }
}
