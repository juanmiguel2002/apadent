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
        'activo',
        'clinica_id'
    ];

    // Relaciones
    public function clinicas()
    {
        return $this->belongsTo(Clinica::class, 'clinica_id');
    }

    public function tratamientos()
    {
        return $this->belongsToMany(Tratamiento::class, 'paciente_trat', 'paciente_id', 'trat_id');
    }
}
