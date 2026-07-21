<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Actualizar la tabla intervenciones_proa
        if (Schema::hasTable('intervenciones_proa')) {
            Schema::table('intervenciones_proa', function (Blueprint $table) {
                // Modificar el ENUM para incluir 'No aplica'
                DB::statement("ALTER TABLE intervenciones_proa MODIFY COLUMN adherencia_proa ENUM('Si', 'No', 'Parcial', 'No aplica') DEFAULT NULL");
            });
        }

        // Actualizar la tabla seguimiento_microbiologico
        if (Schema::hasTable('seguimiento_microbiologico')) {
            Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
                // Modificar el ENUM para incluir 'No aplica'
                DB::statement("ALTER TABLE seguimiento_microbiologico MODIFY COLUMN adherencia_proa ENUM('Si', 'No', 'Parcial', 'No aplica') DEFAULT NULL");
            });
        }
    }

    public function down(): void
    {
        // Revertir a los valores originales
        if (Schema::hasTable('intervenciones_proa')) {
            DB::statement("ALTER TABLE intervenciones_proa MODIFY COLUMN adherencia_proa ENUM('Si', 'No', 'Parcial') DEFAULT NULL");
        }

        if (Schema::hasTable('seguimiento_microbiologico')) {
            DB::statement("ALTER TABLE seguimiento_microbiologico MODIFY COLUMN adherencia_proa ENUM('Si', 'No', 'Parcial') DEFAULT NULL");
        }
    }
};
