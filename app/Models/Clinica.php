<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinica extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'direccion',
        'email',
        'telefono',
        'cif',
        'direccion_fac',
        'cuenta'

    ];
    public function users() {
        return $this->belongsToMany(User::class, 'clinica_user', 'clinica_id', 'user_id');
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'clinica_id');
    }

}
