<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EspTratante extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'esp_tratante';

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
    public static function rules(): array
    {
        return [
            'descripcion' => [
                'required',
                'string',
                'max:150',
                'unique:esp_tratante,descripcion',
            ],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'descripcion.required' => 'La descripción de la especialidad tratante es obligatoria.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'descripcion.max' => 'La descripción no puede exceder los 150 caracteres.',
            'descripcion.unique' => 'Esta especialidad tratante ya existe en el sistema.',
        ];
    }

    /**
     * Scope a query to search for especialidades tratantes.
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('descripcion', 'LIKE', "%{$search}%");
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
