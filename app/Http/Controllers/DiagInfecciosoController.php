<?php

namespace App\Http\Controllers;

use App\Models\DiagInfeccioso;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class DiagInfecciosoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            if ($request->ajax()) {
                $diagInfecciosos = DiagInfeccioso::query();

                // Aplicar búsqueda si existe
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $diagInfecciosos->search($searchValue);
                }

                // Aplicar ordenamiento
                if ($request->has('order')) {
                    $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                    $orderDirection = $request->order[0]['dir'];
                    $diagInfecciosos->orderBy($orderColumn, $orderDirection);
                } else {
                    $diagInfecciosos->orderBy('created_at', 'desc');
                }

                // Obtener total de registros
                $totalRecords = DiagInfeccioso::count();
                $filteredRecords = $diagInfecciosos->count();

                // Aplicar paginación
                $start = $request->get('start', 0);
                $length = $request->get('length', 10);
                $diagInfecciosos = $diagInfecciosos->skip($start)->take($length)->get();

                // Formatear datos para DataTable
                $data = $diagInfecciosos->map(function ($diagInfeccioso) {
                    return [
                        'id' => $diagInfeccioso->id,
                        'descripcion' => $diagInfeccioso->descripcion,
                        'created_at' => $diagInfeccioso->created_at->format('d/m/Y H:i'),
                        'updated_at' => $diagInfeccioso->updated_at->format('d/m/Y H:i'),
                        'actions' => view('diag-infeccioso.partials.actions', compact('diagInfeccioso'))->render(),
                    ];
                });

                return response()->json([
                    'draw' => intval($request->get('draw')),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data' => $data,
                ]);
            }

            return view('diag-infeccioso.index');
        } catch (Exception $e) {
            Log::error('Error en DiagInfecciosoController@index: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error al cargar los datos: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar los diagnósticos infecciosos: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            return view('diag-infeccioso.create');
        } catch (Exception $e) {
            Log::error('Error en DiagInfecciosoController@create: ' . $e->getMessage());
            return redirect()->route('diag-infeccioso.index')
                ->with('error', 'Error al cargar el formulario de creación: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), DiagInfeccioso::rules(), DiagInfeccioso::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DiagInfeccioso::create($request->only(['descripcion']));

            return redirect()->route('diag-infeccioso.index')
                ->with('success', 'Diagnóstico Infeccioso creado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en DiagInfecciosoController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear el diagnóstico infeccioso: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DiagInfeccioso $diagInfeccioso): View|RedirectResponse
    {
        try {
            return view('diag-infeccioso.show', compact('diagInfeccioso'));
        } catch (Exception $e) {
            Log::error('Error en DiagInfecciosoController@show: ' . $e->getMessage());
            return redirect()->route('diag-infeccioso.index')
                ->with('error', 'Error al mostrar el diagnóstico infeccioso: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DiagInfeccioso $diagInfeccioso): View|RedirectResponse
    {
        try {
            return view('diag-infeccioso.edit', compact('diagInfeccioso'));
        } catch (Exception $e) {
            Log::error('Error en DiagInfecciosoController@edit: ' . $e->getMessage());
            return redirect()->route('diag-infeccioso.index')
                ->with('error', 'Error al cargar el formulario de edición: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DiagInfeccioso $diagInfeccioso): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), DiagInfeccioso::rules($diagInfeccioso->id), DiagInfeccioso::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $diagInfeccioso->update($request->only(['descripcion']));

            return redirect()->route('diag-infeccioso.index')
                ->with('success', 'Diagnóstico Infeccioso actualizado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en DiagInfecciosoController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar el diagnóstico infeccioso: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiagInfeccioso $diagInfeccioso): JsonResponse|RedirectResponse
    {
        try {
            $diagInfeccioso->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Diagnóstico Infeccioso eliminado exitosamente.'
                ]);
            }

            return redirect()->route('diag-infeccioso.index')
                ->with('success', 'Diagnóstico Infeccioso eliminado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en DiagInfecciosoController@destroy: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el diagnóstico infeccioso: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('diag-infeccioso.index')
                ->with('error', 'Error al eliminar el diagnóstico infeccioso: ' . $e->getMessage());
        }
    }
}
