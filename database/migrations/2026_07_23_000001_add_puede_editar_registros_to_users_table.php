<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permiso por usuario: "puede editar registros ya guardados".
 *
 * Sustituye el candado rígido por rol: un usuario básico con este permiso
 * activo puede volver a editar formularios que ya registró. Los administradores
 * siempre pueden (no dependen de esta columna).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'puede_editar_registros')) {
                $table->boolean('puede_editar_registros')->default(false)->after('rol');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'puede_editar_registros')) {
                $table->dropColumn('puede_editar_registros');
            }
        });
    }
};
