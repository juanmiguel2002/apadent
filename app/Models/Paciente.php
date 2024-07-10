<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'telefono',
        'num_paciente',
        'fecha_nacimiento',
        'revision',
        'observacion',
        'obser_cbct',
        'odontograma_obser',
        'clinica_id',

    ];
    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'clinica_id');
    }

    public function tratamientos()
    {
        return $this->hasMany(Tratamiento::class, 'paciente_id');
    }
}
