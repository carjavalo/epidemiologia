<?php

namespace App\Http\Controllers;

use App\Models\TipMuestra;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class TipMuestraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            if ($request->ajax()) {
                $tipMuestras = TipMuestra::query();

                // Aplicar búsqueda si existe
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $tipMuestras->search($searchValue);
                }

                // Aplicar ordenamiento
                if ($request->has('order')) {
                    $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                    $orderDirection = $request->order[0]['dir'];
                    $tipMuestras->orderBy($orderColumn, $orderDirection);
                } else {
                    $tipMuestras->orderBy('created_at', 'desc');
                }

                // Obtener total de registros
                $totalRecords = TipMuestra::count();
                $filteredRecords = $tipMuestras->count();

                // Aplicar paginación
                $start = $request->get('start', 0);
                $length = $request->get('length', 10);
                $tipMuestras = $tipMuestras->skip($start)->take($length)->get();

                // Formatear datos para DataTable
                $data = $tipMuestras->map(function ($tipMuestra) {
                    return [
                        'id' => $tipMuestra->id,
                        'descripcion' => $tipMuestra->descripcion,
                        'created_at' => $tipMuestra->created_at->format('d/m/Y H:i'),
                        'updated_at' => $tipMuestra->updated_at->format('d/m/Y H:i'),
                        'actions' => view('tip-muestra.partials.actions', compact('tipMuestra'))->render(),
                    ];
                });

                return response()->json([
                    'draw' => intval($request->get('draw')),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data' => $data,
                ]);
            }

            return view('tip-muestra.index');
        } catch (Exception $e) {
            Log::error('Error en TipMuestraController@index: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error al cargar los datos: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar los tipos de muestra: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            return view('tip-muestra.create');
        } catch (Exception $e) {
            Log::error('Error en TipMuestraController@create: ' . $e->getMessage());
            return redirect()->route('tip-muestra.index')
                ->with('error', 'Error al cargar el formulario de creación: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), TipMuestra::rules(), TipMuestra::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            TipMuestra::create($request->only(['descripcion']));

            return redirect()->route('tip-muestra.index')
                ->with('success', 'Tipo de Muestra creado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en TipMuestraController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear el tipo de muestra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TipMuestra $tipMuestra): View|RedirectResponse
    {
        try {
            return view('tip-muestra.show', compact('tipMuestra'));
        } catch (Exception $e) {
            Log::error('Error en TipMuestraController@show: ' . $e->getMessage());
            return redirect()->route('tip-muestra.index')
                ->with('error', 'Error al mostrar el tipo de muestra: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipMuestra $tipMuestra): View|RedirectResponse
    {
        try {
            return view('tip-muestra.edit', compact('tipMuestra'));
        } catch (Exception $e) {
            Log::error('Error en TipMuestraController@edit: ' . $e->getMessage());
            return redirect()->route('tip-muestra.index')
                ->with('error', 'Error al cargar el formulario de edición: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipMuestra $tipMuestra): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), TipMuestra::rules($tipMuestra->id), TipMuestra::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $tipMuestra->update($request->only(['descripcion']));

            return redirect()->route('tip-muestra.index')
                ->with('success', 'Tipo de Muestra actualizado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en TipMuestraController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar el tipo de muestra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipMuestra $tipMuestra): JsonResponse|RedirectResponse
    {
        try {
            $tipMuestra->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tipo de Muestra eliminado exitosamente.'
                ]);
            }

            return redirect()->route('tip-muestra.index')
                ->with('success', 'Tipo de Muestra eliminado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en TipMuestraController@destroy: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el tipo de muestra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('tip-muestra.index')
                ->with('error', 'Error al eliminar el tipo de muestra: ' . $e->getMessage());
        }
    }
}
