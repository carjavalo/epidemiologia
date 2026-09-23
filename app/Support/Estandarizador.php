<?php

namespace App\Support;

use App\Models\Equivalencia;

/**
 * Convierte el texto crudo que llega de la fuente externa en el valor
 * estandarizado del catálogo correspondiente.
 *
 * El reconocimiento es deliberadamente conservador: coincidencia exacta sobre
 * una clave normalizada, sin parecidos aproximados. En datos clínicos, adivinar
 * "se parece a" puede colocar una muestra en el servicio equivocado; lo que no
 * tiene equivalencia se deja tal cual y aparece en la pantalla de pendientes
 * para que alguien la mapee a mano.
 */
class Estandarizador
{
    public const SERVICIO = 'servicio';
    public const MUESTRA  = 'muestra';

    /** Mapas clave => valor, cargados una sola vez por catálogo. */
    protected static array $cache = [];

    /**
     * Normaliza un texto hasta su "clave": lo que se compara realmente.
     *
     * Absorbe las diferencias mecánicas que trae la fuente:
     *   - acentos y eñes            "PUNCIÓN"  -> "PUNCION"
     *   - mayúsculas/minúsculas     "Orina"    -> "ORINA"
     *   - puntuación y símbolos     "N.A"      -> "N A"
     *   - espacios de más           "A  B"     -> "A B"
     *   - letra pegada a número     "UCI3"     -> "UCI 3"
     */
    public static function clave(?string $texto): string
    {
        $t = trim((string) $texto);

        if ($t === '') {
            return '';
        }

        // Str::ascii transcribe acentos con una tabla propia; no depende de la
        // configuración regional del servidor como sí lo hace iconv.
        $t = \Illuminate\Support\Str::ascii($t);
        $t = strtoupper($t);

        $t = preg_replace('/[^A-Z0-9]+/', ' ', $t);
        $t = preg_replace('/(?<=[A-Z])(?=[0-9])/', ' ', $t);
        $t = preg_replace('/(?<=[0-9])(?=[A-Z])/', ' ', $t);

        return trim(preg_replace('/\s+/', ' ', $t));
    }

    /**
     * Valor estandarizado, o null si el texto no tiene equivalencia conocida.
     */
    public static function estandarizar(string $catalogo, ?string $texto): ?string
    {
        $clave = static::clave($texto);

        if ($clave === '') {
            return null;
        }

        return static::mapa($catalogo)[$clave] ?? null;
    }

    /**
     * Igual que estandarizar(), pero devuelve el texto original cuando no hay
     * equivalencia. Es lo que se usa al importar: nunca se pierde un dato.
     */
    public static function aplicar(string $catalogo, ?string $texto): ?string
    {
        if ($texto === null) {
            return null;
        }

        return static::estandarizar($catalogo, $texto) ?? trim($texto);
    }

    /**
     * Mapa completo clave => valor de un catálogo.
     */
    public static function mapa(string $catalogo): array
    {
        if (! isset(static::$cache[$catalogo])) {
            static::$cache[$catalogo] = Equivalencia::query()
                ->where('catalogo', $catalogo)
                ->pluck('valor', 'clave')
                ->all();
        }

        return static::$cache[$catalogo];
    }

    /**
     * Olvida los mapas en memoria. Hay que llamarlo tras cambiar equivalencias
     * dentro del mismo proceso (seeders, comandos, CRUD).
     */
    public static function olvidarCache(?string $catalogo = null): void
    {
        if ($catalogo === null) {
            static::$cache = [];
            return;
        }

        unset(static::$cache[$catalogo]);
    }
}
