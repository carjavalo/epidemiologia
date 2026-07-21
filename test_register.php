<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $user = User::create([
        'name' => 'testing',
        'apellido1' => 'User',
        'apellido2' => '2',
        'email' => 'testing@test.com',
        'password' => Hash::make('123456'),
    ]);
    
    echo "✓ Usuario creado exitosamente\n";
    echo "ID: {$user->id}\n";
    echo "Email: {$user->email}\n";
    echo "Nombre: {$user->name} {$user->apellido1} {$user->apellido2}\n";
} catch (\Exception $e) {
    echo "✗ ERROR al crear usuario:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
