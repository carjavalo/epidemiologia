<?php

namespace App\Http\Controllers;

use App\Models\Pantimicrobiano;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class PantimicrobianoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            if ($request->ajax()) {
                $pantimicrobianos = Pantimicrobiano::query();

                // Aplicar búsqueda si existe
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $pantimicrobianos->search($searchValue);
                }

                // Aplicar ordenamiento
                if ($request->has('order')) {
                    $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                    $orderDirection = $request->order[0]['dir'];
                    $pantimicrobianos->orderBy($orderColumn, $orderDirection);
                } else {
                    $pantimicrobianos->orderBy('created_at', 'desc');
                }

                $totalRecords = Pantimicrobiano::count();
                $filteredRecords = $pantimicrobianos->count();

                // Aplicar paginación
                if ($request->has('start') && $request->has('length')) {
                    $pantimicrobianos->skip($request->start)->take($request->length);
                }

                $data = $pantimicrobianos->get()->map(function ($pantimicrobiano) {
                    return [
                        'id' => $pantimicrobiano->id,
                        'descripcion' => $pantimicrobiano->descripcion,
                        'created_at' => $pantimicrobiano->created_at->format('d/m/Y H:i'),
                        'updated_at' => $pantimicrobiano->updated_at->format('d/m/Y H:i'),
                        'actions' => view('pantimicrobiano.partials.actions', compact('pantimicrobiano'))->render()
                    ];
                });

                return response()->json([
                    'draw' => intval($request->draw),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data' => $data
                ]);
            }

            return view('pantimicrobiano.index');
        } catch (Exception $e) {
            Log::error('Error en PantimicrobianoController@index: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => 'Error al cargar los datos'], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar la página: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            return view('pantimicrobiano.create');
        } catch (Exception $e) {
            Log::error('Error en PantimicrobianoController@create: ' . $e->getMessage());
            return redirect()->route('pantimicrobiano.index')->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Pantimicrobiano::rules(), Pantimicrobiano::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            Pantimicrobiano::create($request->only(['descripcion']));

            return redirect()->route('pantimicrobiano.index')
                ->with('success', 'Perfil Antimicrobiano creado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en PantimicrobianoController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear el perfil antimicrobiano: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pantimicrobiano $pantimicrobiano): View|RedirectResponse
    {
        try {
            return view('pantimicrobiano.show', compact('pantimicrobiano'));
        } catch (Exception $e) {
            Log::error('Error en PantimicrobianoController@show: ' . $e->getMessage());
            return redirect()->route('pantimicrobiano.index')->with('error', 'Error al mostrar el perfil antimicrobiano: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pantimicrobiano $pantimicrobiano): View|RedirectResponse
    {
        try {
            return view('pantimicrobiano.edit', compact('pantimicrobiano'));
        } catch (Exception $e) {
            Log::error('Error en PantimicrobianoController@edit: ' . $e->getMessage());
            return redirect()->route('pantimicrobiano.index')->with('error', 'Error al cargar el formulario de edición: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pantimicrobiano $pantimicrobiano): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Pantimicrobiano::rules($pantimicrobiano->id), Pantimicrobiano::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $pantimicrobiano->update($request->only(['descripcion']));

            return redirect()->route('pantimicrobiano.index')
                ->with('success', 'Perfil Antimicrobiano actualizado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en PantimicrobianoController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar el perfil antimicrobiano: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pantimicrobiano $pantimicrobiano): JsonResponse
    {
        try {
            $pantimicrobiano->delete();

            return response()->json([
                'success' => true,
                'message' => 'Perfil Antimicrobiano eliminado exitosamente.'
            ]);
        } catch (Exception $e) {
            Log::error('Error en PantimicrobianoController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el perfil antimicrobiano: ' . $e->getMessage()
            ], 500);
        }
    }
}
