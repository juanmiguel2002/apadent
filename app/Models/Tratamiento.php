<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'descripcion'
    ];

    // public function pacientes()
    // {
    //     return $this->belongsToMany(Paciente::class, 'paciente_trat', 'trat_id', 'paciente_id')
    //                 ->withPivot('created_at')
    //                 ->withTimestamps();
    // }

    // Relaciones
    public function pacientes()
    {
        return $this->belongsToMany(Paciente::class, 'paciente_trat', 'trat_id', 'paciente_id');
    }

    public function etapas()
    {
        return $this->belongsToMany(Etapa::class, 'tratamiento_etapa', 'trat_id', 'etapa_id');
    }

    public function tratamientoEtapas()
    {
        return $this->hasMany(TratamientoEtapa::class, 'trat_id');
    }
}
