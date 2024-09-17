<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archivos extends Model
{
    use HasFactory;
    protected $table = 'archivos';
    protected $fillable = [
        'ruta',
        'tipo',
        'paciente_id',
        'paciente_etapa_id',
    ];

    // Relación muchos a uno con pacienteEtapas
    public function PacienteEtapas()
    {
        return $this->belongsTo(PacienteEtapas::class);
    }
}
