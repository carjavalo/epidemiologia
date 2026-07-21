<?php

namespace Database\Seeders;

use App\Models\CategoriaQuirurgica;
use Illuminate\Database\Seeder;

class CategoriaQuirurgicaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'Amputacion de extremidades',
            'Apendicetomia',
            'Cesarea',
            'Cirugia de cabeza y cuello',
            'Cirugia de colon',
            'Cirugia gastrica',
            'Cirugia intestino delgado',
            'Cirugia toraxica',
            'Cirugia vascular',
            'Colecistectomia',
            'Conducto biliar, higado, cirugia pancreatica',
            'Craneotomia',
            'Derivacion',
            'Esplenectomia',
            'Instumentacion',
            'Herniorrafia',
            'Histerectomia abdominal',
            'Histerectomia vaginal',
            'Injerto de piel',
            'Injerto derivacion arteria coronaria',
            'Laparotomia',
            'Lavados ( abdomen)',
            'Mastectomia',
            'Nefrectomia',
            'Otras sistema cardiovascular',
            'Otras sistema respiratorio',
            'Otras, oido, nariz, boca, faringe',
            'Otros procedimientos obstetricos-gineco',
            'Otros sistema digestivo',
            'Otros sistema endocrino',
            'Otros sistema genito-urinario',
            'Otros sistema hematico y linfatico',
            'Otros sistema integumentario',
            'Otros sistema musculo esqueletico',
            'Otros sistema nervioso',
            'Otros visual',
            'Prostatectomia',
            'Protesis de articulacion',
            'Reduccion abierta por fractura',
            'Trasplante de organos',
            'Quemados',
            'Ventriculostomia',
            'Eventorrafia',
            'Endometritis post parto',
            'Revascularizacion miocardica',
        ];

        foreach ($categorias as $descripcion) {
            CategoriaQuirurgica::firstOrCreate(['descripcion' => $descripcion]);
        }
    }
}
