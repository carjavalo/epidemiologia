<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Servicio (ubicación) estandarizado del hospital.
 */
class Servicio extends Model
{
    protected $table = 'servicios';

    protected $fillable = [
        'nombre',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function rules($id = null): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:150',
                'unique:servicios,nombre,' . $id,
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del servicio es obligatorio.',
            'nombre.string'   => 'El nombre debe ser una cadena de texto.',
            'nombre.max'      => 'El nombre no puede exceder los 150 caracteres.',
            'nombre.unique'   => 'Ya existe un servicio con ese nombre.',
        ];
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('nombre', 'LIKE', "%{$search}%");
    }

    /**
     * Equivalencias (textos crudos) que apuntan a este servicio.
     */
    public function equivalencias()
    {
        return $this->hasMany(Equivalencia::class, 'valor', 'nombre')
            ->where('catalogo', Equivalencia::CATALOGO_SERVICIO);
    }
}
