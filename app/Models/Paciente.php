<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'pacientes';

    protected $fillable = [
        'nombre',
        'id_historia',
        'fecha_nacimiento',
        'sexo',
        'identificador_unico',
        'tipo_identificacion',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function seguimientos()
    {
        return $this->hasMany(EpidemiologiaRegistro::class, 'paciente_id');
    }
}
