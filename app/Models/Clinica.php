<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
    // Definir los eventos de Eloquent en el modelo
    protected static function boot()
    {
        parent::boot();

        // Evento que se dispara después de crear una clínica
        static::created(function ($clinica) {
            // Crear carpeta para la clínica y subcarpetas
            $clinica->createClinicaFolder();
        });
    }

    // Método para crear la carpeta de la clínica y sus subcarpetas
    public function createClinicaFolder()
    {
        // Definir el nombre de la carpeta principal usando el nombre de la clínica
        $rutaBase = storage_path('app/public/clinicas');
        $clinicaFolder = $rutaBase .'/'. $this->name;

        // Crear la carpeta principal de la clínica
        Storage::makeDirectory($clinicaFolder);

        // Crear subcarpetas dentro de la carpeta de la clínica
        $subfolders = ['facturas', 'documentos']; // Puedes agregar más subcarpetas aquí
        foreach ($subfolders as $subfolder) {
            Storage::makeDirectory($clinicaFolder . '/' . $subfolder);
        }
    }

    public function users() {
        return $this->belongsToMany(User::class, 'clinica_user', 'clinica_id', 'user_id');
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'clinica_id');
    }

}
