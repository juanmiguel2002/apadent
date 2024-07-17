<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    use HasFactory;

    protected $table = 'images';
    protected $fillable = [
        'trat_etapa_id',
        'ruta',
    ];

    public function tratEtapa()
    {
        return $this->belongsTo(TratEtapa::class, 'trat_etapa_id');
    }
}
