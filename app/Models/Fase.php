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
        return $this->belongsTo(Tratamiento::class);
    }

    public function etapas()
    {
        return $this->hasMany(Etapa::class);
    }

}
