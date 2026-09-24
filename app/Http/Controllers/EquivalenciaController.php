<?php

namespace App\Http\Controllers;

use App\Models\Equivalencia;
use App\Models\Servicio;
use App\Models\Sitio;
use App\Models\TipMuestra;
use App\Support\Estandarizador;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Equivalencias: cómo se escribe cada servicio o tipo de muestra en la fuente
 * externa, y a qué valor estandarizado corresponde.
 */
class EquivalenciaController extends Controller
{
    public function index(Request $request): View
    {
        $equivalencias = Equivalencia::query()
            ->when($request->filled('catalogo'), fn ($q) => $q->where('catalogo', $request->input('catalogo')))
            ->when($request->filled('q'), fn ($q) => $q->search($request->input('q')))
            ->orderBy('catalogo')
            ->orderBy('valor')
            ->orderBy('texto_crudo')
            ->paginate(30)
            ->withQueryString();

        return view('equivalencias.index', [
            'equivalencias' => $equivalencias,
            'catalogos' => Equivalencia::CATALOGOS,
            'totalPendientes' => $this->contarPendientes(),
        ]);
    }

    public function create(): View
    {
        return view('equivalencias.create', $this->opciones());
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Equivalencia::rules(), Equivalencia::messages());

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $catalogo = $request->input('catalogo');
            $clave = Estandarizador::clave($request->input('texto_crudo'));

            if (Equivalencia::where('catalogo', $catalogo)->where('clave', $clave)->exists()) {
                return redirect()->back()
                    ->with('error', 'Ya existe una equivalencia para ese texto en este catálogo.')
                    ->withInput();
            }

            Equivalencia::create($request->only('catalogo', 'texto_crudo', 'valor'));

            return redirect()->route('equivalencias.index')->with('success', 'Equivalencia creada correctamente.');
        } catch (Exception $e) {
            Log::error('Error en EquivalenciaController@store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear la equivalencia: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Equivalencia $equivalencia): View
    {
        return view('equivalencias.edit', $this->opciones() + compact('equivalencia'));
    }

    public function update(Request $request, Equivalencia $equivalencia): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), Equivalencia::rules($equivalencia->id), Equivalencia::messages());

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $equivalencia->update($request->only('catalogo', 'texto_crudo', 'valor'));

            return redirect()->route('equivalencias.index')->with('success', 'Equivalencia actualizada correctamente.');
        } catch (Exception $e) {
            Log::error('Error en EquivalenciaController@update: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Equivalencia $equivalencia): JsonResponse|RedirectResponse
    {
        try {
            $equivalencia->delete();

            return request()->ajax()
                ? response()->json(['success' => true, 'message' => 'Equivalencia eliminada correctamente.'])
                : redirect()->route('equivalencias.index')->with('success', 'Equivalencia eliminada correctamente.');
        } catch (Exception $e) {
            Log::error('Error en EquivalenciaController@destroy: ' . $e->getMessage());

            return request()->ajax()
                ? response()->json(['success' => false, 'message' => $e->getMessage()], 500)
                : redirect()->route('equivalencias.index')->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Textos que aparecen en los datos cargados y todavía no tienen equivalencia.
     */
    public function pendientes(Request $request): View
    {
        $soloCatalogo = $request->input('catalogo');

        if (! array_key_exists((string) $soloCatalogo, Equivalencia::CATALOGOS)) {
            $soloCatalogo = null;
        }

        return view('equivalencias.pendientes', $this->opciones() + [
            'pendientes' => $this->calcularPendientes($soloCatalogo),
            'soloCatalogo' => $soloCatalogo,
        ]);
    }

    /**
     * Crea de golpe las equivalencias marcadas en la pantalla de pendientes.
     */
    public function mapear(Request $request): RedirectResponse
    {
        $asignaciones = $request->input('valor', []);
        $creadas = 0;

        foreach ($asignaciones as $indice => $valor) {
            $valor = trim((string) $valor);
            $catalogo = $request->input("catalogo.{$indice}");
            $crudo = $request->input("texto_crudo.{$indice}");

            if ($valor === '' || ! $catalogo || ! $crudo) {
                continue;
            }

            $clave = Estandarizador::clave($crudo);

            if (Equivalencia::where('catalogo', $catalogo)->where('clave', $clave)->exists()) {
                continue;
            }

            Equivalencia::create([
                'catalogo' => $catalogo,
                'texto_crudo' => $crudo,
                'valor' => $valor,
            ]);
            $creadas++;
        }

        Estandarizador::olvidarCache();

        return redirect()->route('equivalencias.pendientes')->with(
            'success',
            $creadas === 0
                ? 'No se marcó ninguna equivalencia.'
                : "Se crearon {$creadas} equivalencia(s). Los registros ya cargados se actualizan con «Estandarizar registros»."
        );
    }

    /**
     * Valores distintos presentes en los datos que no tienen equivalencia,
     * con cuántos registros los usan.
     *
     * @return array<string, array<int, array{texto: string, registros: int}>>
     */
    /**
     * Términos sin equivalencia por catálogo, listos para la pantalla.
     *
     * @return array<string, array<int, array{texto: string, registros: int}>>
     */
    protected function calcularPendientes(?string $soloCatalogo = null): array
    {
        $resultado = [];

        foreach (array_keys(Equivalencia::CATALOGOS) as $catalogo) {
            if ($soloCatalogo !== null && $catalogo !== $soloCatalogo) {
                continue;
            }

            $sinMapear = Estandarizador::pendientes($catalogo);

            $resultado[$catalogo] = array_map(
                fn ($texto, $registros) => ['texto' => $texto, 'registros' => $registros],
                array_keys($sinMapear),
                array_values($sinMapear)
            );
        }

        return $resultado;
    }

    protected function contarPendientes(): int
    {
        return array_sum(array_map('count', $this->calcularPendientes()));
    }

    /**
     * Valores estandarizados disponibles para elegir en los formularios.
     */
    protected function opciones(): array
    {
        return [
            'catalogos' => Equivalencia::CATALOGOS,
            'valores' => [
                Equivalencia::CATALOGO_SERVICIO => Servicio::orderBy('nombre')->pluck('nombre')->all(),
                Equivalencia::CATALOGO_MUESTRA  => TipMuestra::orderBy('descripcion')->pluck('descripcion')->all(),
                Equivalencia::CATALOGO_SITIO    => Sitio::ordenNatural()->pluck('nombre')->all(),
            ],
        ];
    }
}
