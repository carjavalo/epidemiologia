<?php

namespace App\Console\Commands;

use App\Support\Estandarizador;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Normaliza `tipo_muestra` y `ubicacion` de los registros ya cargados.
 *
 * Por defecto SOLO SIMULA: dice qué cambiaría sin tocar la base. Para aplicar
 * de verdad hay que pasar --aplicar explícitamente.
 */
class EstandarizarRegistros extends Command
{
    protected $signature = 'estandarizar:registros
                            {--aplicar : Escribe los cambios en la base (sin esta opción solo simula)}
                            {--detalle=12 : Cuántas filas mostrar en cada listado}';

    protected $description = 'Simula (o aplica) la estandarización de servicios y tipos de muestra ya cargados';

    protected const TABLA = 'seguimiento_microbiologico';

    protected const COLUMNAS = [
        'tipo_muestra' => Estandarizador::MUESTRA,
        'ubicacion'    => Estandarizador::SERVICIO,
    ];

    public function handle(): int
    {
        $aplicar = (bool) $this->option('aplicar');
        $detalle = max(1, (int) $this->option('detalle'));

        $this->newLine();
        $this->line($aplicar
            ? '<bg=red;fg=white> APLICANDO CAMBIOS EN LA BASE </>'
            : '<bg=blue;fg=white> SIMULACIÓN — no se escribe nada </>');
        $this->newLine();

        $totalCambios = 0;

        foreach (self::COLUMNAS as $columna => $catalogo) {
            $totalCambios += $this->procesar($columna, $catalogo, $aplicar, $detalle);
        }

        $this->newLine();

        if (! $aplicar) {
            $this->line("  En total cambiarían <options=bold>{$totalCambios}</> registros.");
            $this->line('  Para aplicarlo: <options=bold>php artisan estandarizar:registros --aplicar</>');
        } else {
            $this->line("  Actualizados <options=bold>{$totalCambios}</> registros.");
        }

        $this->newLine();

        return self::SUCCESS;
    }

    protected function procesar(string $columna, string $catalogo, bool $aplicar, int $detalle): int
    {
        $this->line("═══ " . strtoupper($columna) . " ═══");

        // Valor distinto => cuántos registros lo tienen
        $conteos = DB::table(self::TABLA)
            ->select($columna . ' as valor', DB::raw('COUNT(*) as n'))
            ->whereNotNull($columna)
            ->where($columna, '<>', '')
            ->groupBy($columna)
            ->pluck('n', 'valor')
            ->all();

        $cambios = [];     // valor original => [nuevo, registros]
        $yaEstaban = 0;
        $pendientes = [];  // valor sin equivalencia => registros

        foreach ($conteos as $original => $n) {
            $nuevo = Estandarizador::estandarizar($catalogo, (string) $original);

            if ($nuevo === null) {
                $pendientes[(string) $original] = $n;
                continue;
            }

            if ($nuevo === (string) $original) {
                $yaEstaban += $n;
                continue;
            }

            $cambios[(string) $original] = [$nuevo, $n];
        }

        $registrosACambiar = array_sum(array_column($cambios, 1));
        $registrosPendientes = array_sum($pendientes);
        $totalRegistros = array_sum($conteos);

        $this->table(
            ['', 'Valores distintos', 'Registros'],
            [
                ['Ya estandarizados', count($conteos) - count($cambios) - count($pendientes), $yaEstaban],
                ['Se estandarizarían', count($cambios), $registrosACambiar],
                ['Sin equivalencia', count($pendientes), $registrosPendientes],
                ['TOTAL', count($conteos), $totalRegistros],
            ]
        );

        if ($cambios !== []) {
            uasort($cambios, fn ($a, $b) => $b[1] <=> $a[1]);
            $this->line("  Los {$detalle} cambios que más registros afectan:");
            foreach (array_slice($cambios, 0, $detalle, true) as $original => [$nuevo, $n]) {
                $this->line(sprintf('    %6d  %-42s → %s', $n, $this->recortar($original, 42), $nuevo));
            }
            $this->newLine();
        }

        if ($pendientes !== []) {
            arsort($pendientes);
            $this->line("  <fg=yellow>Sin equivalencia</> (se quedan como están), los {$detalle} mayores:");
            foreach (array_slice($pendientes, 0, $detalle, true) as $valor => $n) {
                $this->line(sprintf('    %6d  %s', $n, $this->recortar((string) $valor, 60)));
            }
            $this->newLine();
        }

        if ($aplicar && $cambios !== []) {
            DB::transaction(function () use ($columna, $cambios) {
                foreach ($cambios as $original => [$nuevo, $n]) {
                    DB::table(self::TABLA)->where($columna, $original)->update([$columna => $nuevo]);
                }
            });
            $this->info("  ✔ {$registrosACambiar} registros actualizados en {$columna}.");
            $this->newLine();
        }

        return $registrosACambiar;
    }

    protected function recortar(string $texto, int $largo): string
    {
        return mb_strlen($texto) > $largo ? mb_substr($texto, 0, $largo - 1) . '…' : $texto;
    }
}
