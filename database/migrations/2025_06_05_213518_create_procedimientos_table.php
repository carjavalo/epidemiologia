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
        Schema::create('procedimientos', function (Blueprint $table) {
            $table->id();
            $table->integer('Cod_Episodio')->nullable();
            $table->string('Cod_Sala', 20)->nullable();
            $table->string('Nom_Sala', 255)->nullable();
            $table->string('Num_Cama', 20)->nullable();
            $table->dateTime('F_Ingreso')->nullable();
            $table->string('Cod_Eps', 30)->nullable();
            $table->string('Nom_Eps', 255)->nullable();
            $table->integer('Hist_Clinica')->nullable();
            $table->string('Tipo_Ident', 5)->nullable();
            $table->string('Num_Ident', 20)->nullable();
            $table->integer('Edad')->nullable();
            $table->char('Sexo', 1)->nullable();
            $table->string('Servicio', 100)->nullable();
            $table->string('Estado', 50)->nullable();
            $table->string('Medico_Trata', 255)->nullable();
            $table->string('Cod_Diag', 10)->nullable();
            $table->string('CIE10', 10)->nullable();
            $table->string('Diagnostico', 500)->nullable();
            $table->string('Antimicrobiano', 100)->nullable();
            $table->string('Cantidad', 100)->nullable();
            $table->string('Presentacion', 500)->nullable();
            $table->string('Via_Aplicacion', 100)->nullable();
            $table->string('Tiem_Horas', 100)->nullable();
            $table->string('Dias_Antibioticos', 100)->nullable();
            $table->date('Fec_Sumistro')->nullable();
            $table->string('Ho_Sumisnistro', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedimientos');
    }
};
