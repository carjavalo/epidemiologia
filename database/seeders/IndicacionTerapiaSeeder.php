<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IndicacionTerapia;

class IndicacionTerapiaSeeder extends Seeder
{
    /**
     * Ejecutar los seeders para crear indicaciones terapéuticas de ejemplo.
     */
    public function run(): void
    {
        $indicaciones = [
            'Infección del tracto urinario no complicada',
            'Neumonía adquirida en la comunidad',
            'Infección de piel y tejidos blandos',
            'Profilaxis quirúrgica en cirugía abdominal',
            'Sepsis de origen abdominal',
            'Endocarditis bacteriana',
            'Meningitis bacteriana aguda',
            'Infección intraabdominal complicada',
            'Bacteriemia por gram positivos',
            'Infección del sitio quirúrgico',
            'Neumonía nosocomial',
            'Infección por Clostridium difficile',
            'Profilaxis en paciente inmunodeprimido',
            'Infección de catéter vascular',
            'Artritis séptica',
        ];

        foreach ($indicaciones as $descripcion) {
            IndicacionTerapia::create([
                'descripcion' => $descripcion,
            ]);
        }

        $this->command->info('Se crearon ' . count($indicaciones) . ' indicaciones terapéuticas de ejemplo.');
    }
}
