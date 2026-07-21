<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$total      = DB::table('deta_procedimientos')->count();
$pacientes  = DB::table('deta_procedimientos')->distinct('Num_Ident')->count('Num_Ident');
$encabezados = DB::table('encabezados_procedimientos')->count();

echo "Encabezados (importaciones): $encabezados\n";
echo "Registros en deta_procedimientos: $total\n";
echo "Pacientes distintos: $pacientes\n";
