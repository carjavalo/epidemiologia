<?php

namespace App\Models;

use App\Support\Estandarizador;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Una variante conocida del texto crudo y el valor estandarizado al que apunta.
 */
class Equivalencia extends Model
{
    protected $table = 'equivalencias';

    public const CATALOGO_SERVICIO = Estandarizador::SERVICIO;
    public const CATALOGO_MUESTRA  = Estandarizador::MUESTRA;

    public const CATALOGOS = [
        self::CATALOGO_SERVICIO => 'Servicio',
        self::CATALOGO_MUESTRA  => 'Tipo de muestra',
    ];

    protected $fillable = [
        'catalogo',
        'clave',
        'texto_crudo',
        'valor',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * La clave se deriva siempre del texto crudo: no se edita a mano, así no
     * puede quedar desincronizada.
     */
    protected static function booted(): void
    {
        static::saving(function (Equivalencia $eq) {
            $eq->clave = Estandarizador::clave($eq->texto_crudo);
        });

        static::saved(fn (Equivalencia $eq) => Estandarizador::olvidarCache($eq->catalogo));
        static::deleted(fn (Equivalencia $eq) => Estandarizador::olvidarCache($eq->catalogo));
    }

    public static function rules($id = null): array
    {
        return [
            'catalogo'    => ['required', 'in:' . implode(',', array_keys(self::CATALOGOS))],
            'texto_crudo' => ['required', 'string', 'max:190'],
            'valor'       => ['required', 'string', 'max:150'],
        ];
    }

    public static function messages(): array
    {
        return [
            'catalogo.required'    => 'Indica a qué catálogo pertenece.',
            'catalogo.in'          => 'El catálogo no es válido.',
            'texto_crudo.required' => 'El texto que llega de la fuente es obligatorio.',
            'texto_crudo.max'      => 'El texto no puede exceder los 190 caracteres.',
            'valor.required'       => 'Elige el valor estandarizado.',
        ];
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $q) use ($search) {
            $q->where('texto_crudo', 'LIKE', "%{$search}%")
              ->orWhere('valor', 'LIKE', "%{$search}%");
        });
    }

    public function getCatalogoNombreAttribute(): string
    {
        return self::CATALOGOS[$this->catalogo] ?? $this->catalogo;
    }
}
