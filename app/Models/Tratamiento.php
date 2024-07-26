<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function pacientes()
    {
        return $this->belongsToMany(Paciente::class, 'paciente_trat', 'trat_id', 'paciente_id')
                    ->withPivot('created_at')
                    ->withTimestamps();
    }

    public function pacienteTrats()
    {
        return $this->hasMany(PacienteTrat::class, 'trat_id', 'id');
    }

    // Relación uno a muchos con Etapas
    public function etapas()
    {
        return $this->hasMany(Etapa::class);
    }
}
