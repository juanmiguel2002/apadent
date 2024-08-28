<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacienteEtapa extends Model
{
    use HasFactory;

    protected $table = 'paciente_etapas';
    protected $fillable = [
        'fecha_ini',
        'fecha_fin',
        'status',
        'revision',
        'orden',

    ];

    public function tratamientoEtapa()
    {
        return $this->hasOne(TratamientoEtapa::class, 'etapas_id', 'etapas_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}
