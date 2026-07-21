<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador si no existe
        $adminEmail = 'admin@proahuv.com';
        
        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'name' => 'Administrador',
                'apellido1' => 'ProAHUV',
                'apellido2' => 'Sistema',
                'email' => $adminEmail,
                'password' => Hash::make('123456789'),
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Usuario administrador creado: ' . $adminEmail);
        } else {
            $this->command->info('Usuario administrador ya existe: ' . $adminEmail);
        }
    }
}
