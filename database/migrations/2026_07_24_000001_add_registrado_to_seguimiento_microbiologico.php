<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Marca si una muestra de epidemiología ya fue registrada/revisada por el
 * usuario (verde) o si todavía falta por registrar (rojo). Un registro recién
 * importado nace en "falta por registrar" hasta que alguien lo guarda.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            if (!Schema::hasColumn('seguimiento_microbiologico', 'registrado')) {
                $table->boolean('registrado')->default(false)->after('edicion_bloqueada');
            }
        });

        // Las filas que ya se habían bloqueado tras un guardado se consideran
        // registradas, para que no aparezcan en rojo sin motivo.
        if (Schema::hasColumn('seguimiento_microbiologico', 'edicion_bloqueada')) {
            DB::table('seguimiento_microbiologico')
                ->where('edicion_bloqueada', true)
                ->update(['registrado' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            if (Schema::hasColumn('seguimiento_microbiologico', 'registrado')) {
                $table->dropColumn('registrado');
            }
        });
    }
};
