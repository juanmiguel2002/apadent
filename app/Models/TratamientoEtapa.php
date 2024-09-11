<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TratamientoEtapa extends Model
{
    use HasFactory;

    protected $table = 'tratamiento_etapa';

    protected $fillable = [
        'trat_id',
        'etapa_id',
    ];

    // Relaciones
    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'trat_id');
    }

    public function etapa()
    {
        return $this->belongsTo(Etapa::class, 'etapa_id');
    }
}
