<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Estandariza en MAYÚSCULA los nombres de antibióticos ya almacenados, para
 * que un mismo fármaco no aparezca en varios grupos por diferencias de
 * capitalización. Las importaciones nuevas ya se guardan en mayúscula.
 */
class EstandarizarAntibioticos extends Command
{
    protected $signature = 'antibioticos:estandarizar {--dry-run : Muestra cuántas filas cambiarían sin escribir}';

    protected $description = 'Convierte a mayúscula los nombres de antibióticos ya guardados en la base';

    /**
     * Columnas [tabla => columna] que guardan nombres de antibióticos.
     */
    private const OBJETIVOS = [
        'deta_procedimientos'        => 'Antimicrobiano',
        'seguimiento_microbiologico' => 'antimicrobiano',
    ];

    public function handle(): int
    {
        $simulacion = (bool) $this->option('dry-run');
        $totalCambios = 0;

        foreach (self::OBJETIVOS as $tabla => $columna) {
            if (!Schema::hasTable($tabla) || !Schema::hasColumn($tabla, $columna)) {
                $this->warn("Se omite {$tabla}.{$columna} (no existe).");
                continue;
            }

            // Filas cuyo valor no está ya en mayúscula (comparación binaria).
            $pendientes = DB::table($tabla)
                ->whereNotNull($columna)
                ->where($columna, '<>', '')
                ->whereRaw("BINARY {$columna} <> UPPER({$columna})")
                ->count();

            $this->line(str_pad("{$tabla}.{$columna}", 42) . "{$pendientes} por estandarizar");
            $totalCambios += $pendientes;

            if (!$simulacion && $pendientes > 0) {
                DB::table($tabla)
                    ->whereNotNull($columna)
                    ->where($columna, '<>', '')
                    ->update([$columna => DB::raw("UPPER({$columna})")]);
            }
        }

        $this->newLine();
        if ($simulacion) {
            $this->comment("Simulación: {$totalCambios} fila(s) cambiarían. Ejecuta sin --dry-run para aplicar.");
        } else {
            $this->info("Listo. {$totalCambios} fila(s) estandarizadas a mayúscula.");
        }

        return self::SUCCESS;
    }
}
