<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function tratamientos()
    {
        return $this->belongsToMany(Tratamiento::class, 'trat_etapas', 'paciente_id', 'tratamiento_id')
            ->withPivot('created_at')->orderByPivot('created_at','desc');
    }

    public function tratEtapas()
    {
        return $this->belongsToMany(Tratamiento::class, 'trat_etapas')
                    ->withPivot('status')->withTimestamps(); // Incluye el campo adicional de la tabla pivote // Incluye marcas de tiempo si las tienes en la tabla pivote
    }
}
