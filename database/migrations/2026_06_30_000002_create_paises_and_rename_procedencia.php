<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catálogo de países (nacionalidad), basado en la lista de la DIAN/DANE.
        if (!Schema::hasTable('paises')) {
            Schema::create('paises', function (Blueprint $table) {
                $table->id();
                $table->string('codigo', 5)->nullable();
                $table->string('nombre');
                $table->timestamps();
                $table->index('nombre');
            });
        }

        // El campo "procedencia" pasa a llamarse "nacionalidad".
        if (Schema::hasColumn('seguimiento_microbiologico', 'procedencia')
            && !Schema::hasColumn('seguimiento_microbiologico', 'nacionalidad')) {
            Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
                $table->renameColumn('procedencia', 'nacionalidad');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('seguimiento_microbiologico', 'nacionalidad')
            && !Schema::hasColumn('seguimiento_microbiologico', 'procedencia')) {
            Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
                $table->renameColumn('nacionalidad', 'procedencia');
            });
        }

        Schema::dropIfExists('paises');
    }
};
