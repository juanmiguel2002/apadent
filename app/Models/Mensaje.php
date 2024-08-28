<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;
    protected $table = 'mensajes';
    protected $fillable = [
        'users_id',
        'mensaje',
        'paciente_id',
        'etapas_id',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function pacienteEtapa()
    {
        return $this->belongsTo(PacienteEtapa::class, ['paciente_id', 'etapas_id']);
    }
}
