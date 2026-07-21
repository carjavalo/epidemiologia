<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SisInternacional;
use Carbon\Carbon;

class SisInternacionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sistemas = [
            'Clasificación Internacional de Enfermedades (CIE-11) - Sistema de codificación médica estándar mundial',
            'Sistema Internacional de Unidades (SI) - Unidades de medida estándar para laboratorio clínico',
            'Nomenclatura Internacional de Medicamentos (DCI) - Denominaciones comunes internacionales de fármacos',
            'Clasificación Internacional del Funcionamiento, Discapacidad y Salud (CIF) - Marco conceptual de la OMS',
            'Sistema Internacional de Farmacovigilancia - Red global de monitoreo de seguridad de medicamentos'
        ];

        foreach ($sistemas as $descripcion) {
            SisInternacional::create([
                'descripcion' => $descripcion,
                'created_at' => Carbon::now('America/Bogota'),
                'updated_at' => Carbon::now('America/Bogota'),
            ]);
        }

        $this->command->info('Se han creado ' . count($sistemas) . ' sistemas internacionales de prueba.');
    }
}
