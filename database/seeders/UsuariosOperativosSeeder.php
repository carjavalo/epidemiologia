<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crea los usuarios operativos del programa (auxiliares, enfermeros, médicos,
 * coordinadores y epidemiólogo) con sus permisos por área.
 *
 * Idempotente: se identifica por 'usuario', así que re-ejecutarlo no duplica.
 * NO cambia la contraseña de un usuario que ya exista (para no pisar cambios).
 */
class UsuariosOperativosSeeder extends Seeder
{
    private const PASSWORD = '12345678';

    public function run(): void
    {
        $usuarios = [];

        // Auxiliar 1..10 — editan ambas áreas
        for ($i = 1; $i <= 10; $i++) {
            $usuarios[] = ["Auxiliar {$i}", User::ROL_BASICO, true, true];
        }
        // Enfermero 1..5 — editan ambas áreas
        for ($i = 1; $i <= 5; $i++) {
            $usuarios[] = ["Enfermero {$i}", User::ROL_BASICO, true, true];
        }
        // Medico 1..5 — SOLO PROA
        for ($i = 1; $i <= 5; $i++) {
            $usuarios[] = ["Medico {$i}", User::ROL_BASICO, false, true];
        }
        // Coordinadores — administradores (ambas áreas implícitas)
        $usuarios[] = ['coordinador PROA',          User::ROL_ADMIN,  true, true];
        $usuarios[] = ['coordinador EPIDEMIOLOGIA',  User::ROL_ADMIN,  true, true];
        // Epidemiólogo — SOLO epidemiología
        $usuarios[] = ['Epidemiologo', User::ROL_BASICO, true, false];

        foreach ($usuarios as [$nombre, $rol, $editaEpi, $editaProa]) {
            $user = User::firstOrNew(['usuario' => $nombre]);

            $user->name = $nombre;
            $user->rol  = $rol;
            $user->puede_editar_epidemiologia = $editaEpi;
            $user->puede_editar_proa          = $editaProa;

            // Solo asigna la contraseña al crear (no la pisa si ya existía).
            if (!$user->exists) {
                $user->password = Hash::make(self::PASSWORD);
            }

            $user->save();
        }

        $this->command?->info('Usuarios operativos creados/actualizados: ' . count($usuarios) . '. Contraseña de los nuevos: ' . self::PASSWORD);
    }
}
