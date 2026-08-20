<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'usuario',
        'apellido1',
        'apellido2',
        'email',
        'password',
        'rol',
        'perfil',
        'puede_editar_epidemiologia',
        'puede_editar_proa',
        'profile_image',
        'foto',
    ];

    /** Roles disponibles en el sistema. */
    public const ROL_BASICO = 'basico';
    public const ROL_ADMIN  = 'administrador';

    /** Perfiles / cargos (definen qué campos del formulario ve cada quien). */
    public const PERFIL_AUXILIAR     = 'auxiliar';
    public const PERFIL_ENFERMERO    = 'enfermero';
    public const PERFIL_MEDICO       = 'medico';
    public const PERFIL_EPIDEMIOLOGO = 'epidemiologo';

    public const PERFILES = [
        self::PERFIL_AUXILIAR     => 'Auxiliar',
        self::PERFIL_ENFERMERO    => 'Enfermero',
        self::PERFIL_MEDICO       => 'Médico',
        self::PERFIL_EPIDEMIOLOGO => 'Epidemiólogo',
    ];

    /** ¿El usuario es administrador? */
    public function esAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    /** ¿El usuario tiene perfil de enfermero? (ve campos exclusivos del formulario) */
    public function esEnfermero(): bool
    {
        return $this->perfil === self::PERFIL_ENFERMERO;
    }

    /**
     * ¿Puede editar los formularios de EPIDEMIOLOGÍA ya guardados?
     * Los administradores siempre pueden.
     */
    public function puedeEditarEpidemiologia(): bool
    {
        return $this->esAdmin() || (bool) $this->puede_editar_epidemiologia;
    }

    /**
     * ¿Puede editar las intervenciones PROA ya guardadas?
     * Los administradores siempre pueden.
     */
    public function puedeEditarProa(): bool
    {
        return $this->esAdmin() || (bool) $this->puede_editar_proa;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'puede_editar_epidemiologia' => 'boolean',
            'puede_editar_proa' => 'boolean',
        ];
    }

    /**
     * Obtener la URL de la imagen de perfil del usuario
     */
    public function getProfileImageUrlAttribute()
    {
        // Priorizar el campo 'foto' sobre 'profile_image'
        $imageField = $this->foto ?: $this->profile_image;

        if ($imageField && $this->hasProfileImage()) {
            return asset('storage/profile_images/' . $imageField);
        }

        // Generar avatar por defecto con nombre completo
        $fullName = trim($this->name . ' ' . $this->apellido1 . ' ' . $this->apellido2);
        $name = urlencode($fullName);
        return "https://ui-avatars.com/api/?name={$name}&size=200&background=007bff&color=fff&font-size=0.6&rounded=true";
    }

    /**
     * Obtener la URL del avatar pequeño (40x40)
     */
    public function getSmallAvatarAttribute()
    {
        // Priorizar el campo 'foto' sobre 'profile_image'
        $imageField = $this->foto ?: $this->profile_image;

        if ($imageField && $this->hasProfileImage()) {
            return asset('storage/profile_images/' . $imageField);
        }

        // Generar avatar pequeño por defecto con nombre completo
        $fullName = trim($this->name . ' ' . $this->apellido1 . ' ' . $this->apellido2);
        $name = urlencode($fullName);
        return "https://ui-avatars.com/api/?name={$name}&size=40&background=007bff&color=fff&font-size=0.6&rounded=true";
    }

    /**
     * Verificar si el usuario tiene imagen de perfil
     */
    public function hasProfileImage()
    {
        // Priorizar el campo 'foto' sobre 'profile_image'
        $imageField = $this->foto ?: $this->profile_image;
        return !empty($imageField) && file_exists(storage_path('app/public/profile_images/' . $imageField));
    }

    /**
     * Obtener el nombre completo del usuario
     */
    public function getFullNameAttribute()
    {
        return trim($this->name . ' ' . $this->apellido1 . ' ' . $this->apellido2);
    }

    /**
     * Obtener la imagen para AdminLTE
     * Método requerido por AdminLTE para mostrar imagen de usuario
     */
    public function adminlte_image()
    {
        return $this->profile_image_url;
    }

    /**
     * Obtener descripción para AdminLTE (opcional)
     */
    public function adminlte_desc()
    {
        return 'Usuario del Sistema';
    }
}
