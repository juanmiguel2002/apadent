<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacienteTrat extends Model
{
    use HasFactory;

    protected $table = 'paciente_trat';
    public $timestamps = true;

    protected $fillable = [
        'paciente_id',
        'trat_id',
        // Agrega aquí otros atributos que deseas permitir para la asignación masiva
    ];

    // Definir la relación con el modelo Tratamiento
    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'trat_id', 'id');
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class, 'trat_etapa_id');
    }

    public function imagenes()
    {
        return $this->hasMany(Imagen::class, 'trat_etapa_id');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'trat_etapa_id');
    }
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }


}
