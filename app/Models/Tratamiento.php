<?php

namespace App\Models;

use App\Http\Controllers\Pacientes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    use HasFactory;
    protected $table = 'tratamientos';

    protected $fillable = [
        'name',
    ];

    public function pacientes()
    {
        return $this->belongsToMany(Paciente::class, 'trat_etapas')
                    ->withPivot('status')->withTimestamps(); // Incluye el campo adicional de la tabla pivote
    }


    public function etapas()
    {
        return $this->hasMany(TratEtapa::class, 'tratamiento_id');
    }
}
