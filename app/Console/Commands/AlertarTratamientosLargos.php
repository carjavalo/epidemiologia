<?php

namespace App\Console\Commands;

use App\Support\AlertasProa;
use Illuminate\Console\Command;

/**
 * Crea notificaciones para los tratamientos PROA que superan los 7 días.
 * Se puede ejecutar a mano o programado (ver App\Support\AlertasProa).
 */
class AlertarTratamientosLargos extends Command
{
    protected $signature = 'proa:alertar-tratamientos {--dias=30 : Solo cursos iniciados en los últimos N días}';

    protected $description = 'Genera notificaciones para tratamientos de antibiótico que superan los 7 días';

    public function handle(): int
    {
        $creadas = AlertasProa::revisarTratamientosLargos((int) $this->option('dias'));
        $this->info("Notificaciones de tratamientos largos creadas: {$creadas}");

        return self::SUCCESS;
    }
}
