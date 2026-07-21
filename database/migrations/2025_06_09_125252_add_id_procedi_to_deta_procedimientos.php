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
        Schema::table('deta_procedimientos', function (Blueprint $table) {
            // Añadir el campo id_procedi como entero
            $table->unsignedBigInteger('id_procedi')->after('id');
            
            // Añadir la restricción de llave foránea
            $table->foreign('id_procedi')
                  ->references('id_procedimiento')
                  ->on('encabezados_procedimientos')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deta_procedimientos', function (Blueprint $table) {
            // Eliminar la llave foránea
            $table->dropForeign(['id_procedi']);
            
            // Eliminar la columna
            $table->dropColumn('id_procedi');
        });
    }
};
