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
        Schema::table('plantillas_observaciones', function (Blueprint $table) {
            $table->dropColumn('unico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plantillas_observaciones', function (Blueprint $table) {
            $table->string('unico')->unique()->after('id');
        });
    }
};
