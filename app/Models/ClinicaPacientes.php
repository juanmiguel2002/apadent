<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicaPacientes extends Model
{
    use HasFactory;
    protected $table = 'clinicas_pacientes'; // muchos a muchos pacientes a clínicas
    protected $fillable = [
        'clinicas_id',
        'pacientes_id',
    ];
}
