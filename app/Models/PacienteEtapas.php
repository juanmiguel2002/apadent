<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacienteEtapas extends Model
{
    use HasFactory;

    protected $table = 'paciente_etapas';
    protected $fillable = [
        'paciente_id',
        'etapa_id',
        'fecha_ini',
        'fecha_fin',
        'status',
        'revision',
    ];

    // public function tratamientoEtapa()
    // {
    //     return $this->hasOne(TratamientoEtapa::class, 'etapa_id', 'etapa_id');
    // }
    public function isCompleted()
    {
        return $this->status === 'Finalizado';
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'paciente_etapas_id');
    }

    public function etapa()
    {
        return $this->belongsTo(Etapa::class);
    }
    
    public function archivos()
    {
        return $this->hasMany(Archivos::class, 'paciente_id');
    }

}
