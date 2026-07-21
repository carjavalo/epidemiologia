<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_quirurgicas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 150);
            $table->timestamps();
            $table->index('descripcion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_quirurgicas');
    }
};
