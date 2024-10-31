<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;
    protected $table = 'mensajes';
    protected $fillable = [
        'user_id',
        'mensaje',
        'tratamientos_id',
        'paciente_trat_id',
        'paciente_etapas_id',
    ];


    // Relación con el modelo User (usuario que escribió el mensaje)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con el modelo PacienteTrat (tratamiento del paciente)
    public function pacienteTratamiento()
    {
        return $this->belongsTo(PacienteTrat::class, 'paciente_trat_id');
    }

    // Relación con el modelo PacienteEtapa (etapa del tratamiento del paciente)
    public function pacienteEtapa()
    {
        return $this->belongsTo(PacienteEtapas::class, 'paciente_etapas_id');
    }

    // Relación con el modelo Tratamiento (tratamiento relacionado)
    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'tratamientos_id');
    }
}
