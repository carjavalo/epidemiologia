<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Separa el permiso único "puede_editar_registros" en dos permisos por área:
 * epidemiología y PROA. Así un usuario puede editar solo una de las dos.
 * Los administradores tienen ambas de forma implícita (no dependen de estas
 * columnas).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'puede_editar_epidemiologia')) {
                $table->boolean('puede_editar_epidemiologia')->default(false)->after('rol');
            }
            if (!Schema::hasColumn('users', 'puede_editar_proa')) {
                $table->boolean('puede_editar_proa')->default(false)->after('puede_editar_epidemiologia');
            }
        });

        // Migrar el permiso anterior (si existe) a los dos nuevos.
        if (Schema::hasColumn('users', 'puede_editar_registros')) {
            DB::table('users')
                ->where('puede_editar_registros', true)
                ->update([
                    'puede_editar_epidemiologia' => true,
                    'puede_editar_proa'          => true,
                ]);

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('puede_editar_registros');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'puede_editar_registros')) {
                $table->boolean('puede_editar_registros')->default(false)->after('rol');
            }
        });

        if (Schema::hasColumn('users', 'puede_editar_epidemiologia')) {
            DB::table('users')
                ->where('puede_editar_epidemiologia', true)
                ->orWhere('puede_editar_proa', true)
                ->update(['puede_editar_registros' => true]);
        }

        Schema::table('users', function (Blueprint $table) {
            foreach (['puede_editar_epidemiologia', 'puede_editar_proa'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
