<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
    ];
    
    public function pacientes()
    {
        return $this->belongsToMany(Paciente::class, 'trat_etapas')
                    ->withPivot('status'); // Incluye el campo adicional de la tabla pivote
    }

    public function etapas()
    {
        return $this->hasMany(TratEtapa::class, 'tratamiento_id');
    }
}
