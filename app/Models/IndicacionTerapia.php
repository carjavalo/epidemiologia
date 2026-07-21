<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicacionTerapia extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     *
     * @var string
     */
    protected $table = 'indicaciones_terapia';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'descripcion',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope para buscar por descripción.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $descripcion
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscarPorDescripcion($query, $descripcion)
    {
        return $query->where('descripcion', 'like', '%' . $descripcion . '%');
    }

    /**
     * Accessor para obtener la descripción formateada.
     *
     * @return string
     */
    public function getDescripcionFormateadaAttribute()
    {
        return ucfirst(strtolower($this->descripcion));
    }
}
