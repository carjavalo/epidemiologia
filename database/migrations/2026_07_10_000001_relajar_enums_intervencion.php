<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Los campos de intervención venían como ENUM estrictos ('Si','No','No aplica'),
     * pero los datos históricos traen variantes ('No Aplica', 'SI', etc.) que MySQL
     * rechaza en modo STRICT. Se pasan a VARCHAR para no perder filas al importar;
     * los valores se normalizan en el importador y el formulario ya muestra el valor
     * guardado como opción.
     */
    private array $tablas = ['seguimiento_microbiologico', 'intervenciones_proa'];

    private array $columnas = [
        'dosis_adecuada', 'duracion_adecuada', 'cultivo_previo',
        'valoracion_grupo1', 'valoracion_uci', 'ajuste_prescripcion',
        'adherencia_proa', 'adherencia_guias', 'caso_cerrado', 'mortalidad',
    ];

    public function up(): void
    {
        $db = DB::getDatabaseName();

        foreach ($this->tablas as $tabla) {
            foreach ($this->columnas as $col) {
                $info = DB::selectOne(
                    'SELECT DATA_TYPE FROM information_schema.COLUMNS
                     WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                    [$db, $tabla, $col]
                );

                if ($info && strtolower($info->DATA_TYPE) === 'enum') {
                    DB::statement("ALTER TABLE `{$tabla}` MODIFY `{$col}` VARCHAR(50) NULL");
                }
            }
        }
    }

    public function down(): void
    {
        // No se revierte a ENUM: perdería datos que no encajan en los valores originales.
    }
};
