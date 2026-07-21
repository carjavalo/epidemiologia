<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Frecuencia;
use Carbon\Carbon;

class FrecuenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $frecuencias = [
            'Diaria - Una vez al día',
            'Semanal - Una vez por semana',
            'Mensual - Una vez al mes',
            'Cada 8 horas - Tres veces al día',
            'Según necesidad - PRN (Pro Re Nata)'
        ];

        foreach ($frecuencias as $descripcion) {
            Frecuencia::create([
                'descripcion' => $descripcion,
                'created_at' => Carbon::now('America/Bogota'),
                'updated_at' => Carbon::now('America/Bogota'),
            ]);
        }

        $this->command->info('Se han creado ' . count($frecuencias) . ' frecuencias de prueba.');
    }
}
