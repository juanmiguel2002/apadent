<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    use HasFactory;
    protected $table = 'archivos';
    protected $fillable = [
        'etapa_id',
        'ruta',
        'file_type',
    ];

    // Relación muchos a uno con Etapa
    public function etapa()
    {
        return $this->belongsTo(Etapa::class);
    }
}
