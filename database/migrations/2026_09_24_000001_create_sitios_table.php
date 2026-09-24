<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo del campo SITIO del formulario de epidemiología.
 *
 * Hasta ahora sus 64 opciones vivían como un array escrito a mano dentro de
 * registros/index.blade.php, así que añadir una exigía tocar la vista. Pasa a
 * la base para que se administre desde su propio CRUD, igual que servicios y
 * tipos de muestra.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sitios')) {
            return;
        }

        Schema::create('sitios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 190)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sitios');
    }
};
