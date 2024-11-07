<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacienteTrat extends Model
{
    use HasFactory;

    protected $table = 'paciente_trat';
    public $timestamps = true;

    protected $fillable = [
        'paciente_id',
        'trat_id',
    ];

    // Relaciones
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'trat_id');
    }
}
