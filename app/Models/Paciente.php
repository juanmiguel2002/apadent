<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'num_paciente',
        'name',
        'apellidos',
        'email',
        'telefono',
        'fecha_nacimiento',
        'observacion',
        'obser_cbct',
        'odontograma_obser', // observacioones odontograma (pag perfil paciente )
        'activo'
    ];

    // Relaciones
    public function clinicas()
    {
        return $this->belongsToMany(Clinica::class, 'clinicas_pacientes', 'pacientes_id', 'clinicas_id');
    }

    public function tratamientos()
    {
        return $this->hasMany(PacienteTrat::class, 'paciente_id');
    }
}
