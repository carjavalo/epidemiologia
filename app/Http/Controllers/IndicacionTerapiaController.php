<?php

namespace App\Http\Controllers;

use App\Models\IndicacionTerapia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;

class IndicacionTerapiaController extends Controller
{
    /**
     * Mostrar listado de indicaciones terapéuticas.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $indicaciones = IndicacionTerapia::select(['id', 'descripcion', 'created_at', 'updated_at']);

            return DataTables::of($indicaciones)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a href="' . route('indicaciones.show', $row->id) . '" class="btn btn-info btn-sm" title="Ver detalles">';
                    $btn .= '<i class="fas fa-eye"></i></a>';
                    $btn .= '<a href="' . route('indicaciones.edit', $row->id) . '" class="btn btn-warning btn-sm" title="Editar">';
                    $btn .= '<i class="fas fa-edit"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarIndicacion(' . $row->id . ')" title="Eliminar">';
                    $btn .= '<i class="fas fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d/m/Y H:i') : '';
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('d/m/Y H:i') : '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('indicaciones.index');
    }

    /**
     * Mostrar formulario para crear nueva indicación terapéutica.
     *
     * @return View
     */
    public function create(): View
    {
        return view('indicaciones.create');
    }

    /**
     * Almacenar nueva indicación terapéutica.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'descripcion' => 'required|string|max:150|unique:indicaciones_terapia,descripcion',
        ], [
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'descripcion.max' => 'La descripción no puede exceder los 150 caracteres.',
            'descripcion.unique' => 'Esta descripción ya existe en el sistema.',
        ]);

        try {
            IndicacionTerapia::create([
                'descripcion' => trim($request->descripcion),
            ]);

            return redirect()->route('indicaciones.index')
                ->with('success', 'Indicación terapéutica creada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la indicación terapéutica: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalles de una indicación terapéutica específica.
     *
     * @param int $id
     * @return View
     */
    public function show($id): View
    {
        $indicacion = IndicacionTerapia::findOrFail($id);
        return view('indicaciones.show', compact('indicacion'));
    }

    /**
     * Mostrar formulario para editar indicación terapéutica.
     *
     * @param int $id
     * @return View
     */
    public function edit($id): View
    {
        $indicacion = IndicacionTerapia::findOrFail($id);
        return view('indicaciones.edit', compact('indicacion'));
    }

    /**
     * Actualizar indicación terapéutica en la base de datos.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $indicacion = IndicacionTerapia::findOrFail($id);

        $request->validate([
            'descripcion' => 'required|string|max:150|unique:indicaciones_terapia,descripcion,' . $indicacion->id,
        ], [
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'descripcion.max' => 'La descripción no puede exceder los 150 caracteres.',
            'descripcion.unique' => 'Esta descripción ya existe en el sistema.',
        ]);

        try {
            $indicacion->update([
                'descripcion' => trim($request->descripcion),
            ]);

            return redirect()->route('indicaciones.index')
                ->with('success', 'Indicación terapéutica actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la indicación terapéutica: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar indicación terapéutica de la base de datos.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $indicacion = IndicacionTerapia::findOrFail($id);
            $indicacion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Indicación terapéutica eliminada exitosamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la indicación terapéutica: ' . $e->getMessage()
            ], 500);
        }
    }
}
