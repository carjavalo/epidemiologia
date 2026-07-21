<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncabezadoProcedimiento extends Model
{
    use HasFactory;
    
    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'encabezados_procedimientos';
    
    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_procedimiento';
    
    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'fecha_procedimiento',
        'Nom_procedimiento',
    ];
    
    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'fecha_procedimiento' => 'datetime',
    ];
    
    /**
     * Obtiene los detalles de procedimientos asociados a este encabezado.
     */
    public function detalles()
    {
        return $this->hasMany(Procedimiento::class, 'id_procedi', 'id_procedimiento');
    }
}
