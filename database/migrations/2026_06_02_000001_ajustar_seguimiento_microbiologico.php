<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('sexo', 20)->nullable()->change();
        });

        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            $table->string('sexo', 20)->nullable()->change();
            $table->string('cultivo_num', 20)->nullable()->after('microorganismo');
        });
    }

    public function down(): void
    {
        Schema::table('seguimiento_microbiologico', function (Blueprint $table) {
            $table->dropColumn('cultivo_num');
            $table->char('sexo', 1)->nullable()->change();
        });

        Schema::table('pacientes', function (Blueprint $table) {
            $table->char('sexo', 1)->nullable()->change();
        });
    }
};
