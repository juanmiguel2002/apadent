<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etapa extends Model
{
    use HasFactory;

    protected $table = 'etapas';

    protected $fillable = [
        'id',
        'name',
        'fecha_ini',
        'fecha_fin',
        'status',
        'revision',
        'fase_id',
        'paciente_id'
    ];

    // Relaciones
    public function fase()
    {
        return $this->belongsTo(Fase::class, 'fase_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class, 'etapa_id');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class);
    }
}
