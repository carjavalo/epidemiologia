<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Diagnostico extends Model
{
    protected $table = 'diagnosticos';

    protected $fillable = [
        'codigo',
        'descripcion',
    ];

    /** "A09 - Diarrea y gastroenteritis…" — útil para el select buscable. */
    public function getCodigoDescripcionAttribute(): string
    {
        return trim(($this->codigo ? $this->codigo . ' - ' : '') . $this->descripcion);
    }

    public static function rules(?int $ignoreId = null): array
    {
        $unique = 'unique:diagnosticos,codigo' . ($ignoreId ? ',' . $ignoreId : '');

        return [
            'codigo'      => ['required', 'string', 'max:10', $unique],
            'descripcion' => ['required', 'string', 'max:500'],
        ];
    }

    public static function messages(): array
    {
        return [
            'codigo.required'      => 'El código CIE-10 es obligatorio.',
            'codigo.max'           => 'El código no puede exceder los 10 caracteres.',
            'codigo.unique'        => 'Ya existe un diagnóstico con ese código.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.max'      => 'La descripción no puede exceder los 500 caracteres.',
        ];
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('codigo', 'LIKE', "%{$search}%")
                     ->orWhere('descripcion', 'LIKE', "%{$search}%");
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
