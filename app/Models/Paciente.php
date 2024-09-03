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
        return $this->belongsToMany(Etapa::class, 'paciente_etapas', 'paciente_id', 'etapas_id')
                    ->withPivot('fecha_fin', 'status', 'revision', 'orden')
                    ->withTimestamps();
    }

    // relaciona tratamientos con pacientes muchos a muchos
    // public function tratamientos(): BelongsToMany
    // {
    //     return $this->belongsToMany(Tratamiento::class, 'paciente_trat', 'trat_id', 'paciente_id')
    //             ->withPivot('created_at')
    //             ->withTimestamps();
    // }
}
