<?php

namespace App\Http\Controllers;

use App\Models\Microorganismo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class MicroorganismoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            if ($request->ajax()) {
                $microorganismos = Microorganismo::query();

                // Aplicar búsqueda si existe
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $microorganismos->search($searchValue);
                }

                // Aplicar ordenamiento
                if ($request->has('order')) {
                    $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                    $orderDirection = $request->order[0]['dir'];
                    $microorganismos->orderBy($orderColumn, $orderDirection);
                } else {
                    $microorganismos->orderBy('created_at', 'desc');
                }

                // Obtener total de registros
                $totalRecords = Microorganismo::count();
                $filteredRecords = $microorganismos->count();

                // Aplicar paginación
                $start = $request->get('start', 0);
                $length = $request->get('length', 10);
                $microorganismos = $microorganismos->skip($start)->take($length)->get();

                // Formatear datos para DataTable
                $data = $microorganismos->map(function ($microorganismo) {
                    return [
                        'id' => $microorganismo->id,
                        'descripcion' => $microorganismo->descripcion,
                        'created_at' => $microorganismo->created_at->format('d/m/Y H:i'),
                        'updated_at' => $microorganismo->updated_at->format('d/m/Y H:i'),
                        'actions' => view('microorganismo.partials.actions', compact('microorganismo'))->render(),
                    ];
                });

                return response()->json([
                    'draw' => intval($request->get('draw')),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data' => $data,
                ]);
            }

            return view('microorganismo.index');
        } catch (Exception $e) {
            Log::error('Error en MicroorganismoController@index: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error al cargar los datos: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar los microorganismos: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            return view('microorganismo.create');
        } catch (Exception $e) {
            Log::error('Error en MicroorganismoController@create: ' . $e->getMessage());
            return redirect()->route('microorganismo.index')
                ->with('error', 'Error al cargar el formulario de creación: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Microorganismo::rules(), Microorganismo::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            Microorganismo::create($request->only(['descripcion']));

            return redirect()->route('microorganismo.index')
                ->with('success', 'Microorganismo creado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en MicroorganismoController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear el microorganismo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Microorganismo $microorganismo): View|RedirectResponse
    {
        try {
            return view('microorganismo.show', compact('microorganismo'));
        } catch (Exception $e) {
            Log::error('Error en MicroorganismoController@show: ' . $e->getMessage());
            return redirect()->route('microorganismo.index')
                ->with('error', 'Error al mostrar el microorganismo: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Microorganismo $microorganismo): View|RedirectResponse
    {
        try {
            return view('microorganismo.edit', compact('microorganismo'));
        } catch (Exception $e) {
            Log::error('Error en MicroorganismoController@edit: ' . $e->getMessage());
            return redirect()->route('microorganismo.index')
                ->with('error', 'Error al cargar el formulario de edición: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Microorganismo $microorganismo): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Microorganismo::rules($microorganismo->id), Microorganismo::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $microorganismo->update($request->only(['descripcion']));

            return redirect()->route('microorganismo.index')
                ->with('success', 'Microorganismo actualizado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en MicroorganismoController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar el microorganismo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Microorganismo $microorganismo): JsonResponse|RedirectResponse
    {
        try {
            $microorganismo->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Microorganismo eliminado exitosamente.'
                ]);
            }

            return redirect()->route('microorganismo.index')
                ->with('success', 'Microorganismo eliminado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en MicroorganismoController@destroy: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el microorganismo: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('microorganismo.index')
                ->with('error', 'Error al eliminar el microorganismo: ' . $e->getMessage());
        }
    }
}
