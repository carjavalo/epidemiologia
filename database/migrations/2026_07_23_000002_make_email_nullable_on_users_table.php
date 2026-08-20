<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El acceso pasa a ser por 'usuario' + contraseña. El correo deja de pedirse
 * en el registro, así que la columna email debe permitir NULL (antes era NOT
 * NULL UNIQUE). El índice unique se conserva: en MySQL varios NULL no chocan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};
