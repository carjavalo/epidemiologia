<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SisInternacional extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sis_internacional';

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
     * @return array
     */
    public static function rules($id = null)
    {
        return [
            'descripcion' => [
                'required',
                'string',
                'max:150',
                'unique:sis_internacional,descripcion' . ($id ? ",$id" : ''),
            ],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public static function messages()
    {
        return [
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'descripcion.max' => 'La descripción no puede exceder los 150 caracteres.',
            'descripcion.unique' => 'Esta descripción ya existe en el sistema.',
        ];
    }

    /**
     * Scope to search by description.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('descripcion', 'like', "%{$search}%");
    }

    /**
     * Get formatted created date for Colombia timezone.
     *
     * @return string
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->setTimezone('America/Bogota')->format('d/m/Y H:i:s');
    }

    /**
     * Get formatted updated date for Colombia timezone.
     *
     * @return string
     */
    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at->setTimezone('America/Bogota')->format('d/m/Y H:i:s');
    }
}
