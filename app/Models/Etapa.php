<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etapa extends Model
{
    use HasFactory;

    protected $table = 'etapas';

    protected $fillable = [
        'trat_id',
        'name',
        'status',
    ];

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class);
    }

    // Relación uno a muchos con Imagenes
    public function imagenes()
    {
        return $this->hasMany(Imagen::class);
    }

    // Relación uno a muchos con Archivos
    public function archivos()
    {
        return $this->hasMany(Archivo::class);
    }

}
