<?php

namespace Database\Seeders;

use App\Models\Equivalencia;
use App\Models\Servicio;
use App\Models\Sitio;
use App\Models\TipMuestra;
use App\Support\Estandarizador;
use Illuminate\Database\Seeder;

/**
 * Carga el catálogo de servicios y las equivalencias de texto crudo ->
 * valor estandarizado, a partir de los CSV de database/data/.
 *
 * Es idempotente: se puede volver a ejecutar sin duplicar nada.
 */
class EstandarizacionSeeder extends Seeder
{
    public function run(): void
    {
        $this->cargarServicios();
        $this->cargarSitios();
        $this->cargarEquivalencias();
        $this->autoEquivalencias();

        Estandarizador::olvidarCache();
    }

    /**
     * Cada valor ya estandarizado es equivalencia de sí mismo.
     *
     * Sin esto, un dato que YA venía bien escrito ("No Aplica", "9. Orina")
     * se contaba como pendiente de mapear, y la pantalla de pendientes se
     * llenaba de valores que en realidad no hay que tocar.
     */
    protected function autoEquivalencias(): void
    {
        $catalogos = [
            Equivalencia::CATALOGO_SERVICIO => Servicio::pluck('nombre'),
            Equivalencia::CATALOGO_MUESTRA  => TipMuestra::pluck('descripcion'),
            Equivalencia::CATALOGO_SITIO    => Sitio::pluck('nombre'),
        ];

        $nuevas = 0;

        foreach ($catalogos as $catalogo => $valores) {
            foreach ($valores as $valor) {
                $clave = Estandarizador::clave($valor);

                if ($clave === '' || Equivalencia::where('catalogo', $catalogo)->where('clave', $clave)->exists()) {
                    continue;
                }

                Equivalencia::create([
                    'catalogo'    => $catalogo,
                    'texto_crudo' => $valor,
                    'valor'       => $valor,
                ]);
                $nuevas++;
            }
        }

        $this->command?->info("Auto-equivalencias (el valor estándar se reconoce a sí mismo): {$nuevas} nuevas.");
    }

    protected function cargarServicios(): void
    {
        $ruta = database_path('data/servicios.csv');

        if (! is_file($ruta)) {
            $this->command?->error("No se encontró {$ruta}");
            return;
        }

        $nuevos = 0;

        foreach ($this->leerCsv($ruta) as $fila) {
            $nombre = trim($fila['nombre'] ?? '');

            if ($nombre === '') {
                continue;
            }

            $servicio = Servicio::firstOrCreate(['nombre' => $nombre]);

            if ($servicio->wasRecentlyCreated) {
                $nuevos++;
            }
        }

        $this->command?->info("Servicios: {$nuevos} nuevos, " . Servicio::count() . ' en total.');
    }

    protected function cargarSitios(): void
    {
        $ruta = database_path('data/sitios.csv');

        if (! is_file($ruta)) {
            $this->command?->error("No se encontró {$ruta}");
            return;
        }

        $nuevos = 0;

        foreach ($this->leerCsv($ruta) as $fila) {
            $nombre = trim($fila['nombre'] ?? '');

            if ($nombre === '') {
                continue;
            }

            $sitio = Sitio::firstOrCreate(['nombre' => $nombre]);

            if ($sitio->wasRecentlyCreated) {
                $nuevos++;
            }
        }

        $this->command?->info("Sitios: {$nuevos} nuevos, " . Sitio::count() . ' en total.');
    }

    protected function cargarEquivalencias(): void
    {
        $ruta = database_path('data/equivalencias.csv');

        if (! is_file($ruta)) {
            $this->command?->error("No se encontró {$ruta}");
            return;
        }

        $nuevas = 0;
        $actualizadas = 0;
        $conflictos = [];

        foreach ($this->leerCsv($ruta) as $fila) {
            $catalogo = trim($fila['catalogo'] ?? '');
            $crudo    = trim($fila['texto_crudo'] ?? '');
            $valor    = trim($fila['valor'] ?? '');

            if ($catalogo === '' || $crudo === '' || $valor === '') {
                continue;
            }

            $clave = Estandarizador::clave($crudo);
            $existente = Equivalencia::where('catalogo', $catalogo)->where('clave', $clave)->first();

            if ($existente === null) {
                Equivalencia::create([
                    'catalogo'    => $catalogo,
                    'texto_crudo' => $crudo,
                    'valor'       => $valor,
                ]);
                $nuevas++;
                continue;
            }

            // Dos textos distintos que normalizan a la misma clave pero apuntan
            // a valores distintos: hay que mirarlo a mano, no elegir por sorteo.
            if ($existente->valor !== $valor) {
                $conflictos[] = "{$catalogo}: «{$crudo}» quiere «{$valor}» pero «{$existente->texto_crudo}» ya dice «{$existente->valor}»";
                continue;
            }

            $actualizadas++;
        }

        $this->command?->info("Equivalencias: {$nuevas} nuevas, {$actualizadas} ya existían.");

        foreach ($conflictos as $c) {
            $this->command?->warn("  CONFLICTO -> {$c}");
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function leerCsv(string $ruta): array
    {
        $fh = fopen($ruta, 'r');

        if ($fh === false) {
            return [];
        }

        $cabecera = fgetcsv($fh);
        $filas = [];

        while (($linea = fgetcsv($fh)) !== false) {
            if ($linea === [null] || $linea === []) {
                continue;
            }

            $filas[] = array_combine(
                $cabecera,
                array_pad(array_slice($linea, 0, count($cabecera)), count($cabecera), '')
            );
        }

        fclose($fh);

        return $filas;
    }
}
