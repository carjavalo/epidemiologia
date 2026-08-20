<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Agrupa las dosis de un antibiótico en "cursos de tratamiento".
 *
 * Regla: un curso ideal dura 7 días. Una dosis cuya fecha de inicio esté a
 * MENOS de 7 días del inicio del curso pertenece a ese curso; una dosis a 7 o
 * más días (el día 8 en adelante) abre un curso NUEVO.
 *
 * Además, dentro de un curso, las dosis con la misma fecha de inicio se
 * consideran repetidas: se conserva la primera de cada fecha.
 */
class ProaCursos
{
    /** Días que dura idealmente un curso. */
    public const DIAS_CURSO = 7;

    /**
     * @param  iterable  $dosis  Colección de Procedimiento (usa Fec_Sumistro).
     * @return array<int, array{inicio: ?Carbon, representativa: mixed, dosis: Collection}>
     *         Cursos ordenados por fecha de inicio; cada uno con su dosis
     *         representativa (la primera de la primera fecha).
     */
    public static function agrupar($dosis): array
    {
        $ordenadas = collect($dosis)
            ->sortBy(fn ($d) => $d->Fec_Sumistro ? (string) $d->Fec_Sumistro : '9999-12-31')
            ->values();

        $cursos = [];

        foreach ($ordenadas as $dose) {
            $fecha = $dose->Fec_Sumistro
                ? Carbon::parse($dose->Fec_Sumistro)->startOfDay()
                : null;

            $ubicada = false;

            foreach ($cursos as $i => $curso) {
                $mismoNulo = ($fecha === null && $curso['inicio'] === null);
                $dentroDe7 = ($fecha !== null && $curso['inicio'] !== null
                    && $curso['inicio']->diffInDays($fecha) < self::DIAS_CURSO);

                if ($mismoNulo || $dentroDe7) {
                    $cursos[$i]['dosis']->push($dose);
                    $ubicada = true;
                    break;
                }
            }

            if (!$ubicada) {
                $cursos[] = ['inicio' => $fecha, 'dosis' => collect([$dose])];
            }
        }

        // Dosis representativa de cada curso: la primera de la fecha más temprana.
        foreach ($cursos as $i => $curso) {
            $cursos[$i]['representativa'] = $curso['dosis']
                ->sortBy(fn ($d) => $d->Fec_Sumistro ? (string) $d->Fec_Sumistro : '9999-12-31')
                ->first();
        }

        return $cursos;
    }

    /**
     * Día actual del tratamiento (en vivo): días transcurridos desde el inicio
     * hasta hoy, empezando en 1. Devuelve null si no hay fecha de inicio.
     */
    public static function diaActual(?Carbon $inicio): ?int
    {
        if ($inicio === null) {
            return null;
        }

        return $inicio->startOfDay()->diffInDays(Carbon::today()) + 1;
    }
}
