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

    public function users() {
        return $this->belongsToMany(User::class, 'clinica_user', 'clinica_id', 'user_id');
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'clinica_id');
    }

    public function carpetas()
    {
        return $this->hasMany(Carpeta::class, 'clinica_id');
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class, 'clinica_id');
    }

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

        // Verificar si la carpeta ya existe en el sistema de archivos
        if (!Storage::disk('clinicas')->exists($clinicaName)) {
            if (!Storage::disk('clinicas')->makeDirectory($clinicaName)) {
                throw new \Exception("No se pudo crear la carpeta principal de la clínica: {$clinicaName}");
            }

            // Guardar en la base de datos la carpeta principal
            $clinicaFolder = Carpeta::create([
                'nombre' => $this->name,
                'carpeta_id' => null, // Es la carpeta raíz
                'clinica_id' => $this->id
            ]);
        }

        // Subcarpetas a crear
        $subfolders = ['Facturas', 'Pacientes'];

        foreach ($subfolders as $subfolder) {
            $subfolderPath = $clinicaName . '/' . $subfolder;

            if (!Storage::disk('clinicas')->exists($subfolderPath)) {
                if (!Storage::disk('clinicas')->makeDirectory($subfolderPath)) {
                    throw new \Exception("No se pudo crear la subcarpeta: {$subfolderPath}");
                }
            }

            // Guardar la subcarpeta en la base de datos
            Carpeta::create([
                'nombre' => $subfolder,
                'carpeta_id' => $clinicaFolder->id, // Relación con la carpeta principal
                'clinica_id' => $this->id // Relación con la clínica
            ]);
        }
    }

    // Obtener facturas dentro de la carpeta "facturas"
    public function facturasEnCarpetaFacturas()
    {
        return $this->facturas()->whereHas('carpeta', function ($query) {
            $query->where('nombre', 'Facturas');
        });
    }
}
