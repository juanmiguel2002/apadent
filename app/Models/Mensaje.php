<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;
    protected $table = 'mensajes';
    protected $fillable = [
        'user_id',
        'mensaje',
        'tratamientos_id',
        'paciente_trat_id',
        'etapas_id',
    ];


    // Relación con el modelo User (usuario que escribió el mensaje)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function etapas() {
        return $this->belongsTo(Etapa::class, 'etapa_id');
    }
}
