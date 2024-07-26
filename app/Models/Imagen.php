<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    use HasFactory;

    protected $table = 'images';
    protected $fillable = [
        'etapa_id',
        'ruta',
    ];

    // Relación muchos a uno con Etapa
    public function etapa()
    {
        return $this->belongsTo(Etapa::class);
    }
}
