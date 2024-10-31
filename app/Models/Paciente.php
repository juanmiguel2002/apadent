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
        'clinica_id',
        'activo'
    ];

    // Relaciones
    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'clinica_id');
    }

    public function tratamientos()
    {
        return $this->belongsToMany(Tratamiento::class, 'paciente_trat', 'paciente_id', 'trat_id');
    }

    public function etapas()
    {
        return $this->belongsToMany(Etapa::class, 'paciente_etapas', 'paciente_id', 'etapa_id')
                    ->withPivot('fecha_ini','fecha_fin', 'status', 'revision')
                    ->withTimestamps();
    }
    
    public function mensajes()
    {
        return $this->hasMany(Mensaje::class);
    }

    // Relación directa con PacienteEtapas
    // public function pacienteEtapas()
    // {
    //     return $this->hasMany(PacienteEtapas::class, 'paciente_id');
    // }

    // // Relación con los mensajes a través de PacienteEtapas
    // public function mensajes()
    // {
    //     return $this->hasManyThrough(Mensaje::class, PacienteEtapas::class, 'paciente_id', 'etapa_id', 'id', 'etapa_id');
    // }
}
