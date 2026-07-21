<?php

namespace App\Http\Controllers;

use App\Models\CategoriaQuirurgica;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoriaQuirurgicaController extends Controller
{
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            if ($request->ajax()) {
                $categorias = CategoriaQuirurgica::query();

                if ($request->has('search') && !empty($request->search['value'])) {
                    $categorias->search($request->search['value']);
                }

                if ($request->has('order')) {
                    $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                    $orderDirection = $request->order[0]['dir'];
                    $categorias->orderBy($orderColumn, $orderDirection);
                } else {
                    $categorias->orderBy('descripcion', 'asc');
                }

                $totalRecords = CategoriaQuirurgica::count();
                $filteredRecords = $categorias->count();

                $start = $request->get('start', 0);
                $length = $request->get('length', 10);
                $categorias = $categorias->skip($start)->take($length)->get();

                $data = $categorias->map(function ($categoriaQuirurgica) {
                    return [
                        'id' => $categoriaQuirurgica->id,
                        'descripcion' => $categoriaQuirurgica->descripcion,
                        'created_at' => $categoriaQuirurgica->created_at->format('d/m/Y H:i'),
                        'updated_at' => $categoriaQuirurgica->updated_at->format('d/m/Y H:i'),
                        'actions' => view('categoria-quirurgica.partials.actions', compact('categoriaQuirurgica'))->render(),
                    ];
                });

                return response()->json([
                    'draw' => intval($request->get('draw')),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data' => $data,
                ]);
            }

            return view('categoria-quirurgica.index');
        } catch (Exception $e) {
            Log::error('Error en CategoriaQuirurgicaController@index: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error al cargar las categorías quirúrgicas: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar las categorías quirúrgicas: ' . $e->getMessage());
        }
    }

    public function create(): View|RedirectResponse
    {
        try {
            return view('categoria-quirurgica.create');
        } catch (Exception $e) {
            Log::error('Error en CategoriaQuirurgicaController@create: ' . $e->getMessage());
            return redirect()->route('categoria-quirurgica.index')
                ->with('error', 'Error al cargar el formulario de creación: ' . $e->getMessage());
        }
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), CategoriaQuirurgica::rules(), CategoriaQuirurgica::messages());

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            CategoriaQuirurgica::create($request->only(['descripcion']));

            return redirect()->route('categoria-quirurgica.index')
                ->with('success', 'Categoría Quirúrgica creada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en CategoriaQuirurgicaController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear la categoría quirúrgica: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(CategoriaQuirurgica $categoriaQuirurgica): View|RedirectResponse
    {
        try {
            return view('categoria-quirurgica.show', compact('categoriaQuirurgica'));
        } catch (Exception $e) {
            Log::error('Error en CategoriaQuirurgicaController@show: ' . $e->getMessage());
            return redirect()->route('categoria-quirurgica.index')
                ->with('error', 'Error al mostrar la categoría quirúrgica: ' . $e->getMessage());
        }
    }

    public function edit(CategoriaQuirurgica $categoriaQuirurgica): View|RedirectResponse
    {
        try {
            return view('categoria-quirurgica.edit', compact('categoriaQuirurgica'));
        } catch (Exception $e) {
            Log::error('Error en CategoriaQuirurgicaController@edit: ' . $e->getMessage());
            return redirect()->route('categoria-quirurgica.index')
                ->with('error', 'Error al cargar el formulario de edición: ' . $e->getMessage());
        }
    }

    public function update(Request $request, CategoriaQuirurgica $categoriaQuirurgica): RedirectResponse
    {
        try {
            $rules = CategoriaQuirurgica::rules();
            $rules['descripcion'][] = 'unique:categorias_quirurgicas,descripcion,' . $categoriaQuirurgica->id;

            $validator = Validator::make($request->all(), $rules, CategoriaQuirurgica::messages());

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $categoriaQuirurgica->update($request->only(['descripcion']));

            return redirect()->route('categoria-quirurgica.index')
                ->with('success', 'Categoría Quirúrgica actualizada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en CategoriaQuirurgicaController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar la categoría quirúrgica: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(CategoriaQuirurgica $categoriaQuirurgica): JsonResponse|RedirectResponse
    {
        try {
            $descripcion = $categoriaQuirurgica->descripcion;
            $categoriaQuirurgica->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Categoría Quirúrgica '{$descripcion}' eliminada exitosamente."
                ]);
            }

            return redirect()->route('categoria-quirurgica.index')
                ->with('success', "Categoría Quirúrgica '{$descripcion}' eliminada exitosamente.");
        } catch (Exception $e) {
            Log::error('Error en CategoriaQuirurgicaController@destroy: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la categoría quirúrgica: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('categoria-quirurgica.index')
                ->with('error', 'Error al eliminar la categoría quirúrgica: ' . $e->getMessage());
        }
    }
}
