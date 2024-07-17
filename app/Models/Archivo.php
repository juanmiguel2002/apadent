<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    use HasFactory;
    protected $table = 'archivos';
    protected $fillable = [
        'trat_etapa_id',
        'ruta',
    ];

    public function tratEtapa()
    {
        return $this->belongsTo(TratEtapa::class);
    }
}
