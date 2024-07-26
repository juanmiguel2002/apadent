<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    // relaciona tratamientos con pacientes muchos a muchos
    public function tratamientos(): BelongsToMany
    {
        return $this->belongsToMany(Tratamiento::class, 'paciente_trat', 'trat_id', 'paciente_id')
                    ->withPivot('created_at')
                    ->withTimestamps();
    }
}
