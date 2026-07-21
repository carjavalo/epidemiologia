<?php

namespace App\Http\Controllers;

use App\Models\SisInternacional;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class SisInternacionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        try {
            if ($request->ajax()) {
                $sistemas = SisInternacional::query();

                // Aplicar búsqueda si existe
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $sistemas->search($searchValue);
                }

                // Aplicar ordenamiento
                if ($request->has('order')) {
                    $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                    $orderDirection = $request->order[0]['dir'];
                    $sistemas->orderBy($orderColumn, $orderDirection);
                } else {
                    $sistemas->orderBy('created_at', 'desc');
                }

                $totalRecords = SisInternacional::count();
                $filteredRecords = $sistemas->count();

                // Aplicar paginación
                if ($request->has('start') && $request->has('length')) {
                    $sistemas->skip($request->start)->take($request->length);
                }

                $data = $sistemas->get()->map(function ($sistema) {
                    return [
                        'id' => $sistema->id,
                        'descripcion' => $sistema->descripcion,
                        'created_at' => $sistema->created_at_formatted,
                        'updated_at' => $sistema->updated_at_formatted,
                        'actions' => view('sis-internacional.partials.actions', compact('sistema'))->render()
                    ];
                });

                return response()->json([
                    'draw' => intval($request->draw),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data' => $data
                ]);
            }

            return view('sis-internacional.index');
        } catch (Exception $e) {
            Log::error('Error en SisInternacionalController@index: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => 'Error al cargar los datos'], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar los sistemas internacionales.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('sis-internacional.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), SisInternacional::rules(), SisInternacional::messages());

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

            $sistema = SisInternacional::create($request->only(['descripcion']));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sistema internacional creado exitosamente.',
                    'data' => $sistema
                ]);
            }

            return redirect()->route('sis-internacional.index')
                ->with('success', 'Sistema internacional creado exitosamente.');

        } catch (Exception $e) {
            Log::error('Error en SisInternacionalController@store: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el sistema internacional.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al crear el sistema internacional.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SisInternacional $sisInternacional): View
    {
        return view('sis-internacional.show', compact('sisInternacional'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SisInternacional $sisInternacional): View
    {
        return view('sis-internacional.edit', compact('sisInternacional'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SisInternacional $sisInternacional): RedirectResponse|JsonResponse
    {
        try {
            $validator = Validator::make(
                $request->all(),
                SisInternacional::rules($sisInternacional->id),
                SisInternacional::messages()
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

            $sisInternacional->update($request->only(['descripcion']));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sistema internacional actualizado exitosamente.',
                    'data' => $sisInternacional
                ]);
            }

            return redirect()->route('sis-internacional.index')
                ->with('success', 'Sistema internacional actualizado exitosamente.');

        } catch (Exception $e) {
            Log::error('Error en SisInternacionalController@update: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el sistema internacional.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al actualizar el sistema internacional.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SisInternacional $sisInternacional): JsonResponse
    {
        try {
            $descripcion = $sisInternacional->descripcion;
            $sisInternacional->delete();

            return response()->json([
                'success' => true,
                'message' => "Sistema internacional '{$descripcion}' eliminado exitosamente."
            ]);

        } catch (Exception $e) {
            Log::error('Error en SisInternacionalController@destroy: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el sistema internacional.'
            ], 500);
        }
    }
}
