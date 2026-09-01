<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear usuario administrador para Epidemiología Hospitalaria';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = 'admin@proahuv.com';
        $password = '123456789';
        
        // Verificar si el usuario ya existe
        if (User::where('email', $email)->exists()) {
            $this->info('El usuario administrador ya existe.');
            
            // Mostrar información del usuario existente
            $user = User::where('email', $email)->first();
            $this->table(
                ['ID', 'Nombre', 'Email', 'Creado'],
                [[$user->id, $user->name . ' ' . $user->apellido1, $user->email, $user->created_at]]
            );
            
            return;
        }
        
        // Crear el usuario
        $user = User::create([
            'name' => 'Administrador',
            'apellido1' => 'Epidemiologia',
            'apellido2' => 'Sistema',
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);
        
        $this->info('Usuario administrador creado exitosamente!');
        $this->table(
            ['Campo', 'Valor'],
            [
                ['Email', $email],
                ['Contraseña', $password],
                ['ID', $user->id],
                ['Nombre completo', $user->name . ' ' . $user->apellido1 . ' ' . $user->apellido2]
            ]
        );
        
        // Verificar que la contraseña funciona
        if (Hash::check($password, $user->password)) {
            $this->info('✅ Verificación de contraseña exitosa.');
        } else {
            $this->error('❌ Error en la verificación de contraseña.');
        }
    }
}
