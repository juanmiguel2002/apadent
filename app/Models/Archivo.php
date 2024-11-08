<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    use HasFactory;

    protected $table = 'archivos';
    protected $fillable = [
        'ruta',
        'tipo',
        'etapa_id',
    ];

    // Relación muchos a uno con pacienteEtapas
    public function etapas()
    {
        return $this->belongsTo(Etapa::class, 'etapa_id');
    }
}
