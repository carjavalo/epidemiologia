<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiagnosticosSeeder extends Seeder
{
    /**
     * Carga el catálogo CIE-10 desde database/data/cie10_diagnosticos.csv.
     * Es idempotente: hace upsert por 'codigo', así que se puede re-ejecutar.
     */
    public function run(): void
    {
        $ruta = database_path('data/cie10_diagnosticos.csv');

        if (!is_file($ruta)) {
            $this->command?->error("No se encontró el CSV: {$ruta}");
            return;
        }

        $fh = fopen($ruta, 'r');
        $encabezado = fgetcsv($fh); // codigo, descripcion

        $ahora = now();
        $lote = [];
        $total = 0;

        while (($fila = fgetcsv($fh)) !== false) {
            $codigo = trim((string) ($fila[0] ?? ''));
            $descripcion = trim((string) ($fila[1] ?? ''));
            if ($codigo === '' && $descripcion === '') {
                continue;
            }

            $lote[] = [
                'codigo'      => mb_substr($codigo, 0, 10),
                'descripcion' => mb_substr($descripcion, 0, 500),
                'created_at'  => $ahora,
                'updated_at'  => $ahora,
            ];
            $total++;

            if (count($lote) >= 500) {
                DB::table('diagnosticos')->upsert($lote, ['codigo'], ['descripcion', 'updated_at']);
                $lote = [];
            }
        }
        if ($lote) {
            DB::table('diagnosticos')->upsert($lote, ['codigo'], ['descripcion', 'updated_at']);
        }
        fclose($fh);

        $this->command?->info("Diagnósticos CIE-10 cargados/actualizados: {$total}");
    }
}
