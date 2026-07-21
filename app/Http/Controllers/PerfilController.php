<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class PerfilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            if ($request->ajax()) {
                try {
                    $perfiles = Perfil::select(['id', 'descripcion', 'created_at', 'updated_at']);

                    if ($request->has('search') && !empty($request->search['value'])) {
                        $searchValue = $request->search['value'];
                        $perfiles->where(function($query) use ($searchValue) {
                            $query->where('descripcion', 'like', '%' . $searchValue . '%');
                        });
                    }

                    $totalRecords = Perfil::count();
                    $filteredRecords = $perfiles->count();

                    if ($request->has('order')) {
                        $orderColumn = $request->columns[$request->order[0]['column']]['name'];
                        $orderDirection = $request->order[0]['dir'];
                        $perfiles->orderBy($orderColumn, $orderDirection);
                    } else {
                        $perfiles->orderBy('id', 'desc');
                    }

                    if ($request->has('start') && $request->has('length')) {
                        $perfiles->skip($request->start)->take($request->length);
                    }

                    $data = $perfiles->get()->map(function ($perfil) {
                        return [
                            'id' => $perfil->id,
                            'descripcion' => $perfil->descripcion,
                            'created_at' => $perfil->created_at ? $perfil->created_at->format('d/m/Y H:i:s') : 'N/A',
                            'updated_at' => $perfil->updated_at ? $perfil->updated_at->format('d/m/Y H:i:s') : 'N/A',
                            'actions' => view('perfiles.partials.actions', compact('perfil'))->render()
                        ];
                    });

                    return response()->json([
                        'draw' => intval($request->draw),
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $filteredRecords,
                        'data' => $data
                    ]);
                } catch (Exception $ajaxException) {
                    return response()->json([
                        'error' => 'Error processing request: ' . $ajaxException->getMessage()
                    ], 500);
                }
            }

            return view('perfiles.index');
        } catch (Exception $e) {
            Log::error('Error en PerfilController@index: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'Error al cargar los perfiles'], 500);
            }
            return redirect()->back()->with('error', 'Error al cargar los perfiles: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            return view('perfiles.create');
        } catch (Exception $e) {
            Log::error('Error en PerfilController@create: ' . $e->getMessage());
            return redirect()->route('perfiles.index')->with('error', 'Error al mostrar el formulario de creación: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Perfil::rules(), Perfil::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            Perfil::create($request->only(['descripcion']));

            return redirect()->route('perfiles.index')
                ->with('success', 'Perfil creado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en PerfilController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear el perfil: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Perfil $perfil): View|RedirectResponse
    {
        try {
            // Verificar que el perfil existe
            if (!$perfil->exists) {
                return redirect()->route('perfiles.index')
                    ->with('error', 'El perfil solicitado no existe.');
            }

            return view('perfiles.show', compact('perfil'));
        } catch (Exception $e) {
            Log::error('Error en PerfilController@show: ' . $e->getMessage());
            return redirect()->route('perfiles.index')->with('error', 'Error al mostrar el perfil: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Perfil $perfil): View|RedirectResponse
    {
        try {
            // Verificar que el perfil existe
            if (!$perfil->exists) {
                return redirect()->route('perfiles.index')
                    ->with('error', 'El perfil solicitado no existe.');
            }

            return view('perfiles.edit', compact('perfil'));
        } catch (Exception $e) {
            Log::error('Error en PerfilController@edit: ' . $e->getMessage());
            return redirect()->route('perfiles.index')->with('error', 'Error al mostrar el formulario de edición: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Perfil $perfil): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Perfil::rules($perfil->id), Perfil::messages());

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $perfil->update($request->only(['descripcion']));

            return redirect()->route('perfiles.index')
                ->with('success', 'Perfil actualizado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en PerfilController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar el perfil: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Perfil $perfil): JsonResponse
    {
        try {
            $perfil->delete();
            return response()->json([
                'success' => true,
                'message' => 'Perfil eliminado exitosamente.'
            ]);
        } catch (Exception $e) {
            Log::error('Error en PerfilController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el perfil: ' . $e->getMessage()
            ], 500);
        }
    }
}
