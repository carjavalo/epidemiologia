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
        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            $table->string('tipo_id', 50)->nullable()->after('paciente_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            $table->dropColumn('tipo_id');
        });
    }
};
