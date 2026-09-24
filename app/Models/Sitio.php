<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Opción del campo SITIO del formulario de epidemiología.
 */
class Sitio extends Model
{
    protected $table = 'sitios';

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
                'max:190',
                'unique:sitios,nombre,' . $id,
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del sitio es obligatorio.',
            'nombre.string'   => 'El nombre debe ser una cadena de texto.',
            'nombre.max'      => 'El nombre no puede exceder los 190 caracteres.',
            'nombre.unique'   => 'Ya existe un sitio con ese nombre.',
        ];
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('nombre', 'LIKE', "%{$search}%");
    }

    /**
     * Equivalencias (textos crudos) que apuntan a este sitio.
     */
    public function equivalencias()
    {
        return $this->hasMany(Equivalencia::class, 'valor', 'nombre')
            ->where('catalogo', Equivalencia::CATALOGO_SITIO);
    }

    /**
     * Orden natural del catálogo: por el número que abre cada opción
     * ("2. Absceso..." va antes que "10. ..."), no alfabético.
     */
    public function scopeOrdenNatural(Builder $query): Builder
    {
        return $query->orderByRaw('CAST(nombre AS UNSIGNED), nombre');
    }
}
