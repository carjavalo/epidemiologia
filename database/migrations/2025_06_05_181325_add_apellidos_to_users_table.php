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
        Schema::table('users', function (Blueprint $table) {
            // Añadir nuevos campos
            $table->string('apellido1', 100)->nullable()->after('name');
            $table->string('apellido2', 100)->nullable()->after('apellido1');
            
            // Modificar longitud del campo name
            $table->string('name', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar los campos añadidos
            $table->dropColumn(['apellido1', 'apellido2']);
            
            // Restaurar el campo name a su longitud original
            $table->string('name')->change();
        });
    }
};
