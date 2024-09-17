<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etapa extends Model
{
    use HasFactory;

    protected $table = 'etapas';

    protected $fillable = [
        'id',
        'name',
    ];

    // Relaciones
    public function tratamientos()
    {
        return $this->belongsToMany(Tratamiento::class, 'tratamiento_etapa', 'etapa_id', 'trat_id');
    }

    public function pacientes()
    {
        return $this->belongsToMany(Paciente::class, 'paciente_etapas', 'etapa_id', 'paciente_id');
    }
    public function pacienteEtapas()
    {
        return $this->hasMany(PacienteEtapas::class, 'etapa_id');
    }
}
