<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('encabezados_procedimientos', function (Blueprint $table) {
            // Primero eliminamos la clave primaria actual si existe
            if (Schema::hasColumn('encabezados_procedimientos', 'id')) {
                $table->dropColumn('id');
            }
            
            // Luego agregamos la nueva clave primaria y los campos requeridos
            if (!Schema::hasColumn('encabezados_procedimientos', 'id_procedimiento')) {
                $table->id('id_procedimiento')->first();
            }
            
            if (!Schema::hasColumn('encabezados_procedimientos', 'fecha_procedimiento')) {
                $table->dateTime('fecha_procedimiento')->after('id_procedimiento');
            }
            
            if (!Schema::hasColumn('encabezados_procedimientos', 'Nom_procedimiento')) {
                $table->string('Nom_procedimiento', 255)->after('fecha_procedimiento');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encabezados_procedimientos', function (Blueprint $table) {
            if (Schema::hasColumn('encabezados_procedimientos', 'id_procedimiento')) {
                $table->dropColumn('id_procedimiento');
            }
            
            if (Schema::hasColumn('encabezados_procedimientos', 'fecha_procedimiento')) {
                $table->dropColumn('fecha_procedimiento');
            }
            
            if (Schema::hasColumn('encabezados_procedimientos', 'Nom_procedimiento')) {
                $table->dropColumn('Nom_procedimiento');
            }
            
            // Restaurar la columna id original
            $table->id();
        });
    }
};
