<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinica extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'responsable',
        'email',
        'telefono',
        'direccion',
        'cp',
        'localidad',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class,);
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'clinica_id');
    }

}
