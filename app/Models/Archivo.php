<?php

namespace App\Models;

use App\Casts\DateFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    use HasFactory;

    protected $table = 'archivos';
    protected $fillable = [
        'name',
        'ruta',
        'tipo',
        'extension',
        'etapa_id',
        'carpeta_id',
        'paciente_id'
    ];

    // Relación muchos a uno con pacienteEtapas
    public function etapas()
    {
        return $this->belongsTo(Etapa::class, 'etapa_id');
    }

    public function carpeta(){
        return $this->belongsTo(Carpeta::class);
    }
    
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    protected $casts = [
        'created_at' => DateFormat::class,
    ];
}
