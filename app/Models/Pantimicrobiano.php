<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pantimicrobiano extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos
     */
    protected $table = 'pantimicrobiano';

    /**
     * Los atributos que son asignables en masa.
     */
    protected $fillable = [
        'descripcion',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Reglas de validación para el modelo
     */
    public static function rules($id = null)
    {
        return [
            'descripcion' => 'required|string|max:150|unique:pantimicrobiano,descripcion,' . $id,
        ];
    }

    /**
     * Mensajes de validación personalizados
     */
    public static function messages()
    {
        return [
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'descripcion.max' => 'La descripción no puede exceder los 150 caracteres.',
            'descripcion.unique' => 'Ya existe un perfil antimicrobiano con esta descripción.',
        ];
    }

    /**
     * Scope para búsquedas
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('descripcion', 'like', '%' . $search . '%');
    }
}
