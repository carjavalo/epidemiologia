<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'usuario')) {
                $table->string('usuario', 100)->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('users', 'rol')) {
                $table->string('rol', 20)->default('basico')->after('email');
            }
        });

        // Backfill: usuario = name (con manejo de colisiones) y rol = basico.
        $taken = [];
        foreach (DB::table('users')->orderBy('id')->get() as $u) {
            if (!empty($u->usuario)) {
                $taken[] = $u->usuario;
                continue;
            }
            $base = trim((string) ($u->name ?: ('usuario' . $u->id)));
            if ($base === '') {
                $base = 'usuario' . $u->id;
            }
            $cand = $base;
            $i = 1;
            while (in_array($cand, $taken, true)) {
                $cand = $base . $i;
                $i++;
            }
            DB::table('users')->where('id', $u->id)->update(['usuario' => $cand]);
            $taken[] = $cand;
        }

        // Asegurar que todos tengan rol (por si el default no se aplicó a filas previas).
        DB::table('users')->whereNull('rol')->orWhere('rol', '')->update(['rol' => 'basico']);

        // Promover al usuario más antiguo a administrador (para que exista al menos uno).
        $primero = DB::table('users')->orderBy('id')->first();
        if ($primero) {
            DB::table('users')->where('id', $primero->id)->update(['rol' => 'administrador']);
        }

        // Bloqueo de edición por registro (una vez que un usuario básico lo guarda).
        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            if (!Schema::hasColumn('seguimiento_microbiologico', 'edicion_bloqueada')) {
                $table->boolean('edicion_bloqueada')->default(false);
            }
        });
        Schema::table('intervenciones_proa', function (Blueprint $table) {
            if (!Schema::hasColumn('intervenciones_proa', 'edicion_bloqueada')) {
                $table->boolean('edicion_bloqueada')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'usuario')) {
                $table->dropUnique(['usuario']);
                $table->dropColumn('usuario');
            }
            if (Schema::hasColumn('users', 'rol')) {
                $table->dropColumn('rol');
            }
        });
        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            if (Schema::hasColumn('seguimiento_microbiologico', 'edicion_bloqueada')) {
                $table->dropColumn('edicion_bloqueada');
            }
        });
        Schema::table('intervenciones_proa', function (Blueprint $table) {
            if (Schema::hasColumn('intervenciones_proa', 'edicion_bloqueada')) {
                $table->dropColumn('edicion_bloqueada');
            }
        });
    }
};
