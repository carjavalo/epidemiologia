<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CategoriaQuirurgica extends Model
{
    protected $table = 'categorias_quirurgicas';

    protected $fillable = [
        'descripcion',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function rules(): array
    {
        return [
            'descripcion' => [
                'required',
                'string',
                'max:150',
                'unique:categorias_quirurgicas,descripcion',
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'descripcion.required' => 'La descripción de la categoría quirúrgica es obligatoria.',
            'descripcion.string'   => 'La descripción debe ser un texto válido.',
            'descripcion.max'      => 'La descripción no puede exceder los 150 caracteres.',
            'descripcion.unique'   => 'Esta categoría quirúrgica ya existe en el sistema.',
        ];
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('descripcion', 'LIKE', "%{$search}%");
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
