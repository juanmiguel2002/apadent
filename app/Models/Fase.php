<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fase extends Model
{
    use HasFactory;
    protected $table = 'fases';
    protected $fillable = [
        'name',
        'trat_id',
    ];
    // Relaciones
    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'trat_id');  // 'trat_id' es la columna que referencia 'tratamientos' en 'fases'
    }

    public function etapas()
    {
        return $this->hasMany(Etapa::class, 'fases_id');
    }
}
