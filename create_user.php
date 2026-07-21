<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Eliminar si ya existe
App\Models\User::where('email', 'admin@proahuv.com')->delete();

$user = App\Models\User::create([
    'name'      => 'Admin',
    'apellido1' => 'HUV',
    'apellido2' => '',
    'email'     => 'admin@proahuv.com',
    'password'  => Illuminate\Support\Facades\Hash::make('proahuv2026'),
]);

echo "Usuario creado: ID {$user->id}\n";
