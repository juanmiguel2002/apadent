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
        // Definir el nombre limpio de la carpeta principal usando el nombre de la clínica
        $clinicaName = preg_replace('/\s+/', '_', trim($this->name));
        // Reemplazar espacios con guiones bajos y eliminar espacios en blanco al inicio y al final
        $clinicaFolder = 'clinicas/' . $clinicaName;

        // Crear la carpeta principal de la clínica usando el disco 'public'
        if (!Storage::disk('public')->exists($clinicaFolder)) {
            if (!Storage::disk('public')->makeDirectory($clinicaFolder)) {
                throw new \Exception("No se pudo crear la carpeta principal de la clínica: {$clinicaFolder}");
            }
        }

        // Crear subcarpetas dentro de la carpeta de la clínica
        $subfolders = ['facturas', 'documentos']; // Puedes agregar más subcarpetas aquí
        foreach ($subfolders as $subfolder) {
            $subfolderPath = $clinicaFolder . '/' . $subfolder;
            if (!Storage::disk('public')->exists($subfolderPath)) {
                if (!Storage::disk('public')->makeDirectory($subfolderPath)) {
                    throw new \Exception("No se pudo crear la subcarpeta: {$subfolderPath}");
                }
            }
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
