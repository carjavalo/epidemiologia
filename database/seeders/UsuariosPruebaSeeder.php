<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('12345678');

        $usuarios = [];

        // Auxiliar 1 - 10 (rol básico)
        for ($i = 1; $i <= 10; $i++) {
            $usuarios[] = [
                'name'       => "Auxiliar{$i}",
                'apellido1'  => 'Prueba',
                'apellido2'  => 'HUV',
                'email'      => "auxiliar{$i}@prueba.huv",
                'password'   => $password,
                'rol'        => User::ROL_BASICO,
                'email_verified_at' => now(),
            ];
        }

        // Enfermero 1 - 5 (rol básico)
        for ($i = 1; $i <= 5; $i++) {
            $usuarios[] = [
                'name'       => "Enfermero{$i}",
                'apellido1'  => 'Prueba',
                'apellido2'  => 'HUV',
                'email'      => "enfermero{$i}@prueba.huv",
                'password'   => $password,
                'rol'        => User::ROL_BASICO,
                'email_verified_at' => now(),
            ];
        }

        // Medico 1 - 5 (rol básico)
        for ($i = 1; $i <= 5; $i++) {
            $usuarios[] = [
                'name'       => "Medico{$i}",
                'apellido1'  => 'Prueba',
                'apellido2'  => 'HUV',
                'email'      => "medico{$i}@prueba.huv",
                'password'   => $password,
                'rol'        => User::ROL_BASICO,
                'email_verified_at' => now(),
            ];
        }

        // Coordinador PROA (admin)
        $usuarios[] = [
            'name'       => 'Coordinador',
            'apellido1'  => 'PROA',
            'apellido2'  => 'HUV',
            'email'      => 'coordinador.proa@prueba.huv',
            'password'   => $password,
            'rol'        => User::ROL_ADMIN,
            'email_verified_at' => now(),
        ];

        // Coordinador EPIDEMIOLOGÍA (admin)
        $usuarios[] = [
            'name'       => 'Coordinador',
            'apellido1'  => 'Epidemiologia',
            'apellido2'  => 'HUV',
            'email'      => 'coordinador.epidemiologia@prueba.huv',
            'password'   => $password,
            'rol'        => User::ROL_ADMIN,
            'email_verified_at' => now(),
        ];

        foreach ($usuarios as $datos) {
            User::updateOrCreate(
                ['email' => $datos['email']],
                $datos
            );
        }

        $this->command->info('✔ Creados/actualizados ' . count($usuarios) . ' usuarios de prueba.');
        $this->command->table(
            ['Email', 'Rol'],
            collect($usuarios)->map(fn ($u) => [$u['email'], $u['rol']])->toArray()
        );
    }
}
