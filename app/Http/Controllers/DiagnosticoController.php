<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DiagnosticoController extends Controller
{
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            if ($request->ajax()) {
                $query = Diagnostico::query();

                if ($request->has('search') && !empty($request->search['value'])) {
                    $query->search($request->search['value']);
                }

                if ($request->has('order')) {
                    $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                    $orderColumn = in_array($orderColumn, ['id', 'codigo', 'descripcion', 'created_at', 'updated_at']) ? $orderColumn : 'codigo';
                    $query->orderBy($orderColumn, $request->order[0]['dir']);
                } else {
                    $query->orderBy('codigo', 'asc');
                }

                $totalRecords = Diagnostico::count();
                $filteredRecords = $query->count();

                $registros = $query->skip($request->get('start', 0))->take($request->get('length', 10))->get();

                $data = $registros->map(fn ($d) => [
                    'id'          => $d->id,
                    'codigo'      => $d->codigo,
                    'descripcion' => $d->descripcion,
                    'created_at'  => $d->created_at?->format('d/m/Y H:i'),
                    'actions'     => view('diagnosticos.partials.actions', ['diagnostico' => $d])->render(),
                ]);

                return response()->json([
                    'draw'            => intval($request->get('draw')),
                    'recordsTotal'    => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data'            => $data,
                ]);
            }

            return view('diagnosticos.index');
        } catch (Exception $e) {
            Log::error('Error en DiagnosticoController@index: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'Error al cargar los diagnósticos: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error al cargar los diagnósticos: ' . $e->getMessage());
        }
    }

    public function create(): View
    {
        return view('diagnosticos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Diagnostico::rules(), Diagnostico::messages());
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            Diagnostico::create($request->only(['codigo', 'descripcion']));

            return redirect()->route('diagnosticos.index')->with('success', 'Diagnóstico creado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en DiagnosticoController@store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear el diagnóstico: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Diagnostico $diagnostico): View
    {
        return view('diagnosticos.show', compact('diagnostico'));
    }

    public function edit(Diagnostico $diagnostico): View
    {
        return view('diagnosticos.edit', compact('diagnostico'));
    }

    public function update(Request $request, Diagnostico $diagnostico): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Diagnostico::rules($diagnostico->id), Diagnostico::messages());
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $diagnostico->update($request->only(['codigo', 'descripcion']));

            return redirect()->route('diagnosticos.index')->with('success', 'Diagnóstico actualizado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error en DiagnosticoController@update: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el diagnóstico: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Diagnostico $diagnostico): JsonResponse|RedirectResponse
    {
        try {
            $codigo = $diagnostico->codigo;
            $diagnostico->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => "Diagnóstico '{$codigo}' eliminado exitosamente."]);
            }
            return redirect()->route('diagnosticos.index')->with('success', "Diagnóstico '{$codigo}' eliminado exitosamente.");
        } catch (Exception $e) {
            Log::error('Error en DiagnosticoController@destroy: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
            }
            return redirect()->route('diagnosticos.index')->with('error', 'Error al eliminar el diagnóstico: ' . $e->getMessage());
        }
    }
}
