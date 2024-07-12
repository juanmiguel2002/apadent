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
        'email',
        'telefono',
        'fecha_nacimiento',
        'revision',
        'observacion',
        'obser_cbct',
        'odontograma_obser', // observacioones odontograma (pag perfil paciente )
        'clinica_id',
    ];
    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'clinica_id');
    }

    // public function tratamiento()
    // {
    //     return $this->hasMany(Tratamiento::class, 'paciente_id');
    // }

    public function tratEtapas()
    {
        return $this->belongsToMany(Tratamiento::class, 'trat_etapas')
                    ->withPivot('status') // Incluye el campo adicional de la tabla pivote
                    ->withTimestamps(); // Incluye marcas de tiempo si las tienes en la tabla pivote
    }
}
