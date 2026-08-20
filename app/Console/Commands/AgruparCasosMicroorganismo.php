<?php

namespace App\Console\Commands;

use App\Models\CasoMicroorganismo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Backfill de la Fase 1 de normalización.
 *
 * Crea un caso de microorganismo por cada combinación (paciente, microorganismo)
 * de las filas ya existentes en seguimiento_microbiologico, sube hacia el caso
 * los datos complementarios y enlaza cada muestra con su caso.
 *
 * Regla para valores divergentes: gana el PRIMER valor no nulo recorriendo las
 * filas del grupo por fecha de toma de muestra (las filas sin fecha van al
 * final). Nada se sobrescribe en silencio: toda discrepancia real entre dos
 * filas del mismo grupo queda listada en un CSV para revisión.
 */
class AgruparCasosMicroorganismo extends Command
{
    protected $signature = 'epidemiologia:agrupar-casos
                            {--dry-run : Simula la agrupación sin escribir en la base}
                            {--rehacer : Reagrupa también las muestras que ya tienen caso_id (no toca casos manuales)}';

    protected $description = 'Crea los casos de microorganismo a partir de las filas existentes de seguimiento_microbiologico';

    public function handle(): int
    {
        $simulacion = (bool) $this->option('dry-run');
        $rehacer    = (bool) $this->option('rehacer');

        if (!DB::getSchemaBuilder()->hasColumn('seguimiento_microbiologico', 'caso_id')) {
            $this->error('Falta la columna caso_id. Ejecuta primero: php artisan migrate');
            return self::FAILURE;
        }

        $campos = CasoMicroorganismo::CAMPOS_COMPLEMENTARIOS;

        $consulta = DB::table('seguimiento_microbiologico')
            ->select(array_merge(
                ['id', 'paciente_id', 'caso_id', 'identificador_unico', 'microorganismo', 'fecha_toma_muestra', 'edicion_bloqueada'],
                $campos
            ))
            // Las filas sin fecha no deben ganar la carrera del "primer valor no nulo".
            ->orderByRaw('fecha_toma_muestra IS NULL, fecha_toma_muestra ASC, id ASC');

        if ($rehacer) {
            // Nunca se deshace una agrupación hecha a mano con los checkboxes.
            $consulta->where(function ($q) {
                $q->whereNull('caso_id')
                  ->orWhereNotIn('caso_id', function ($sub) {
                      $sub->select('id')->from('casos_microorganismo')
                          ->where('origen', CasoMicroorganismo::ORIGEN_MANUAL);
                  });
            });
        } else {
            $consulta->whereNull('caso_id');
        }

        $filas = $consulta->get();

        if ($filas->isEmpty()) {
            $this->info('No hay muestras pendientes de agrupar.');
            return self::SUCCESS;
        }

        // ── Agrupar por (paciente, microorganismo normalizado) ──────────────
        $grupos = [];
        foreach ($filas as $fila) {
            $clavePaciente = $fila->paciente_id !== null
                ? 'P:' . $fila->paciente_id
                : 'D:' . trim((string) $fila->identificador_unico);

            $grupos[$clavePaciente . '|' . CasoMicroorganismo::normalizar($fila->microorganismo)][] = $fila;
        }

        $this->info(sprintf(
            '%d muestra(s) → %d caso(s) de microorganismo.%s',
            $filas->count(),
            count($grupos),
            $simulacion ? '  [SIMULACIÓN — no se escribe nada]' : ''
        ));

        $divergencias = [];
        $casosCreados = 0;
        $muestrasLigadas = 0;

        $ejecutar = function () use ($grupos, $campos, $simulacion, &$divergencias, &$casosCreados, &$muestrasLigadas) {
            $barra = $this->output->createProgressBar(count($grupos));
            $barra->start();

            foreach ($grupos as $filasGrupo) {
                $primera = $filasGrupo[0];

                $datosCaso = [
                    'paciente_id'         => $primera->paciente_id,
                    'microorganismo'      => $primera->microorganismo,
                    'microorganismo_norm' => CasoMicroorganismo::normalizar($primera->microorganismo),
                    'origen'              => CasoMicroorganismo::ORIGEN_AUTOMATICO,
                    'edicion_bloqueada'   => false,
                ];

                // Primer valor no nulo por columna + detección de discrepancias.
                foreach ($campos as $campo) {
                    $vistos = [];

                    foreach ($filasGrupo as $fila) {
                        $valor = $fila->{$campo};
                        if ($valor === null || $valor === '') {
                            continue;
                        }
                        if (!array_key_exists($campo, $datosCaso)) {
                            $datosCaso[$campo] = $valor;
                        }
                        $vistos[(string) $valor][] = $fila->id;
                    }

                    if (count($vistos) > 1) {
                        $divergencias[] = [
                            'paciente_id'         => $primera->paciente_id,
                            'identificador_unico' => $primera->identificador_unico,
                            'microorganismo'      => $primera->microorganismo,
                            'columna'             => $campo,
                            'valor_conservado'    => $datosCaso[$campo] ?? '',
                            'valores_en_conflicto' => implode(' || ', array_map(
                                fn ($v, $ids) => $v . ' (filas ' . implode(',', $ids) . ')',
                                array_keys($vistos),
                                $vistos
                            )),
                        ];
                    }
                }

                // El caso queda bloqueado si cualquiera de sus muestras lo estaba.
                foreach ($filasGrupo as $fila) {
                    if ((int) $fila->edicion_bloqueada === 1) {
                        $datosCaso['edicion_bloqueada'] = true;
                        break;
                    }
                }

                if (!$simulacion) {
                    $datosCaso['created_at'] = now();
                    $datosCaso['updated_at'] = now();

                    $casoId = DB::table('casos_microorganismo')->insertGetId($datosCaso);

                    DB::table('seguimiento_microbiologico')
                        ->whereIn('id', array_column($filasGrupo, 'id'))
                        ->update(['caso_id' => $casoId]);
                }

                $casosCreados++;
                $muestrasLigadas += count($filasGrupo);
                $barra->advance();
            }

            $barra->finish();
            $this->newLine(2);
        };

        if ($simulacion) {
            $ejecutar();
        } else {
            DB::transaction($ejecutar);
        }

        $this->info("Casos creados:    {$casosCreados}");
        $this->info("Muestras ligadas: {$muestrasLigadas}");

        // Al rehacer, los casos automáticos anteriores quedan sin muestras.
        if ($rehacer && !$simulacion) {
            $huerfanos = DB::table('casos_microorganismo')
                ->where('origen', CasoMicroorganismo::ORIGEN_AUTOMATICO)
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))->from('seguimiento_microbiologico')
                      ->whereColumn('seguimiento_microbiologico.caso_id', 'casos_microorganismo.id');
                })
                ->delete();

            if ($huerfanos) {
                $this->info("Casos automáticos vacíos eliminados: {$huerfanos}");
            }
        }

        if ($divergencias) {
            $ruta = $this->escribirReporte($divergencias);
            $this->warn(sprintf(
                '%d discrepancia(s) entre filas del mismo caso. Se conservó el primer valor no nulo.',
                count($divergencias)
            ));
            $this->warn("Reporte para revisión: {$ruta}");
        } else {
            $this->info('Sin discrepancias entre filas duplicadas.');
        }

        if ($simulacion) {
            $this->newLine();
            $this->comment('Simulación terminada. Vuelve a ejecutar sin --dry-run para aplicar.');
        }

        return self::SUCCESS;
    }

    /**
     * Vuelca las discrepancias a un CSV (UTF-8 con BOM, para que Excel lo abra bien).
     */
    private function escribirReporte(array $divergencias): string
    {
        $directorio = storage_path('app/reportes');
        if (!is_dir($directorio)) {
            mkdir($directorio, 0775, true);
        }

        $ruta = $directorio . DIRECTORY_SEPARATOR . 'casos_divergencias_' . date('Ymd_His') . '.csv';

        $manejador = fopen($ruta, 'w');
        fwrite($manejador, "\xEF\xBB\xBF");
        fputcsv($manejador, ['paciente_id', 'identificador_unico', 'microorganismo', 'columna', 'valor_conservado', 'valores_en_conflicto'], ';');
        foreach ($divergencias as $fila) {
            fputcsv($manejador, $fila, ';');
        }
        fclose($manejador);

        return $ruta;
    }
}
