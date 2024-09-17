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
        'etapa_id',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function etapaPaciente()
    {
        return $this->belongsTo(PacienteEtapas::class, 'etapas_id');
    }
}
