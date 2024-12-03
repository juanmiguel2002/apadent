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

    // Relaciones
    public function pacientes()
    {
        return $this->hasMany(PacienteTrat::class, 'trat_id');
    }
    public function fases()
    {
        return $this->hasMany(Fase::class, 'trat_id');
    }
}
