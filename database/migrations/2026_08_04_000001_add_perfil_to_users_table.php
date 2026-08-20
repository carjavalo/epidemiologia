<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'perfil')) {
            Schema::table('users', function (Blueprint $table) {
                // auxiliar | enfermero | medico | epidemiologo | null
                $table->string('perfil', 30)->nullable()->after('rol');
            });
        }

        // Relleno inicial según el nombre/usuario con que se sembraron las cuentas
        // (Enfermero 1, Auxiliar 3, Medico 2, Epidemiologo, …).
        $mapa = [
            'enfermero'    => 'enfermero',
            'auxiliar'     => 'auxiliar',
            'medico'       => 'medico',
            'epidemiologo' => 'epidemiologo',
        ];
        foreach ($mapa as $patron => $perfil) {
            DB::table('users')
                ->whereNull('perfil')
                ->where(function ($q) use ($patron) {
                    $q->where('usuario', 'like', $patron . '%')
                      ->orWhere('name', 'like', $patron . '%');
                })
                ->update(['perfil' => $perfil]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'perfil')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('perfil');
            });
        }
    }
};
