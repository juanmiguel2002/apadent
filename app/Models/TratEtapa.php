<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TratEtapa extends Model
{
    use HasFactory;
    
    protected $table = 'trat_etapas';

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'tratamiento_id');
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
}
