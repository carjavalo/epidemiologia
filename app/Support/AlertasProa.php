<?php

namespace App\Support;

use App\Models\Notificacion;
use App\Models\Procedimiento;
use Carbon\Carbon;

/**
 * Revisa los cursos de antibiótico y crea una notificación por cada tratamiento
 * que ya superó los 7 días (día 8 en adelante). Deduplica por curso, así que se
 * puede llamar muchas veces sin repetir avisos.
 */
class AlertasProa
{
    /**
     * @param  int  $dias  Solo considera cursos iniciados en los últimos N días
     *                     (evita marcar tratamientos antiguos del histórico).
     * @return int  Notificaciones creadas.
     */
    public static function revisarTratamientosLargos(int $dias = 30): int
    {
        $desde = Carbon::today()->subDays($dias);

        // Pares (documento, antibiótico) con al menos una dosis reciente.
        $pares = Procedimiento::query()
            ->whereNotNull('Fec_Sumistro')
            ->where('Fec_Sumistro', '>=', $desde->format('Y-m-d'))
            ->whereNotNull('Num_Ident')->where('Num_Ident', '<>', '')
            ->whereNotNull('Antimicrobiano')->where('Antimicrobiano', '<>', '')
            ->select('Num_Ident', 'Antimicrobiano')
            ->distinct()
            ->get();

        $creadas = 0;

        foreach ($pares as $par) {
            $dosis = Procedimiento::where('Num_Ident', $par->Num_Ident)
                ->where('Antimicrobiano', $par->Antimicrobiano)
                ->get(['id', 'Num_Ident', 'Antimicrobiano', 'Fec_Sumistro']);

            foreach (ProaCursos::agrupar($dosis) as $curso) {
                $inicio = $curso['inicio'];
                $dia = ProaCursos::diaActual($inicio);

                // Solo cursos que superan los 7 días y que son recientes.
                if ($dia === null || $dia <= 7 || $inicio->lt($desde)) {
                    continue;
                }

                $rep = $curso['representativa'] ?? null;
                if (!$rep) {
                    continue;
                }

                $notif = Notificacion::crear([
                    'tipo'             => 'proa',
                    'titulo'           => 'Tratamiento supera 7 días: ' . $par->Antimicrobiano,
                    'mensaje'          => 'El tratamiento de ' . $par->Antimicrobiano
                                          . ' (paciente doc. ' . $par->Num_Ident . ') iniciado el '
                                          . $inicio->format('d/m/Y') . ' lleva ' . $dia
                                          . ' días, superando los 7 días ideales.',
                    'url'              => Notificacion::urlRegistroPaciente($par->Num_Ident),
                    // Referencia distinta a la de "curso nuevo" para no colisionar.
                    'referencia_tabla' => 'curso_largo',
                    'referencia_id'    => $rep->id,
                ]);

                if ($notif) {
                    $creadas++;
                }
            }
        }

        return $creadas;
    }
}
