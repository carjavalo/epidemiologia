<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Tratamiento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tratamientos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'descripcion',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Validation rules for the model.
     *
     * @return array<string, mixed>
     */
    public static function rules($id = null): array
    {
        return [
            'descripcion' => [
                'required',
                'string',
                'max:250',
                'unique:tratamientos,descripcion,' . $id,
            ],
        ];
    }

    /**
     * Validation messages for the model.
     *
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no puede exceder los 250 caracteres.',
            'descripcion.unique' => 'Ya existe un tratamiento con esta descripción.',
        ];
    }

    /**
     * Scope para búsqueda global en el modelo.
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('descripcion', 'LIKE', "%{$search}%");
    }
}
