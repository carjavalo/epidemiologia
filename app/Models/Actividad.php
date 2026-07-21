<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'user_id', 'user_nombre', 'tipo', 'accion', 'descripcion',
        'referencia_tabla', 'referencia_id', 'paciente', 'ip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Registrar una actividad del usuario autenticado.
     * Uso: Actividad::registrar(['tipo' => 'epidemiologia', 'accion' => 'crear', 'descripcion' => '...']);
     */
    public static function registrar(array $data): self
    {
        $user = auth()->user();

        return static::create(array_merge([
            'user_id'     => $user?->id,
            'user_nombre' => $user ? trim($user->name . ' ' . ($user->apellido1 ?? '') . ' ' . ($user->apellido2 ?? '')) : 'Sistema',
            'ip'          => request()?->ip(),
        ], $data));
    }
}
