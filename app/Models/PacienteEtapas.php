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
        // 'orden', //ordenar las etapas del paciente

    ];

    public function tratamientoEtapa()
    {
        return $this->hasOne(TratamientoEtapa::class, 'etapas_id', 'etapas_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'etapa_id', 'paciente_id');
    }
    public function etapa()
    {
        return $this->belongsTo(Etapa::class, 'etapa_id');
    }
}
