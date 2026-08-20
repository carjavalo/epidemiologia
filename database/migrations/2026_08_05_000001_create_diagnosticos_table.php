<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('diagnosticos')) {
            Schema::create('diagnosticos', function (Blueprint $table) {
                $table->id();
                $table->string('codigo', 10)->unique();   // CIE-10, ej. A09
                $table->string('descripcion', 500);
                $table->timestamps();

                $table->index('descripcion');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosticos');
    }
};
