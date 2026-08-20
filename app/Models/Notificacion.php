<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'tipo', 'titulo', 'mensaje', 'url', 'leida',
        'referencia_tabla', 'referencia_id',
    ];

    protected $casts = [
        'leida' => 'boolean',
    ];

    /**
     * Crea una notificación evitando duplicados por referencia (si se indica).
     * Devuelve null si ya existía una con la misma referencia.
     */
    public static function crear(array $datos): ?self
    {
        if (!empty($datos['referencia_tabla']) && !empty($datos['referencia_id'])) {
            $existe = static::where('referencia_tabla', $datos['referencia_tabla'])
                ->where('referencia_id', $datos['referencia_id'])
                ->exists();
            if ($existe) {
                return null;
            }
        }

        return static::create($datos);
    }

    /**
     * URL a la página de registros posicionada en el paciente (servicio +
     * búsqueda por documento), para que el clic en la notificación lleve al
     * microorganismo / tratamiento correspondiente y no a la lista general.
     */
    public static function urlRegistroPaciente(?string $documento): string
    {
        $documento = trim((string) $documento);
        if ($documento === '') {
            return route('registros.index', [], false);
        }

        // Servicio (ubicación) más reciente del paciente, para abrir su bloque.
        $ubicacion = EpidemiologiaRegistro::where('identificador_unico', $documento)
            ->whereNotNull('ubicacion')
            ->where('ubicacion', '!=', '')
            ->orderByDesc('fecha_toma_muestra')
            ->value('ubicacion');

        // URL RELATIVA (tercer parámetro false): funciona sin importar el host/
        // puerto por el que se acceda (localhost:8003, 192.168.2.151:8003, etc.).
        return route('registros.index', array_filter([
            'servicio' => $ubicacion,
            'search'   => $documento,
        ]), false);
    }

    /** Ícono FontAwesome según el tipo. */
    public function getIconoAttribute(): string
    {
        return [
            'proa'          => 'fa-capsules',
            'epidemiologia' => 'fa-vial',
            'sistema'       => 'fa-info-circle',
        ][$this->tipo] ?? 'fa-bell';
    }

    /** Color del ícono según el tipo. */
    public function getColorAttribute(): string
    {
        return [
            'proa'          => 'text-success',
            'epidemiologia' => 'text-info',
            'sistema'       => 'text-secondary',
        ][$this->tipo] ?? 'text-primary';
    }
}
