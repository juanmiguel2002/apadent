<?php

namespace App\Http\Controllers;

use App\Models\Clinica;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ClinicaController extends Controller
{
    //Controlador de la clinica pasada por id
    public function index($id) {
        // Obtener la clínica especificada por el ID
        $clinica = Clinica::findOrFail($id);

        // Obtener solo los usuarios relacionados con esta clínica que son doctores
        $users = $clinica->users()->with('roles')->whereHas('roles', function($query) {
            $query->where('name', 'doctor_admin'); // Filtrar por rol "doctor_admin"
        })->get();

        return view('clinicas.clinica-show', compact('clinica', 'users'));
    }

    public function mostrarVistaPdf($ruta)
    {
        return view('ver_pdf', compact('ruta'));
    }
    public function verPdfPrivado($ruta)
    {
        // Asegurar que el archivo existe
        if (!Storage::disk('local')->exists("clinicas/$ruta")) {
            abort(404, 'El archivo no existe.');
        }

        // Obtener el archivo
        $file = Storage::disk('local')->get("clinicas/$ruta");
        $mimeType = Storage::disk('local')->mimeType("clinicas/$ruta");
        $rutaIcon = asset('storage/recursos/imagenes/favicon.png');
        // Retornar la respuesta con el contenido del archivo y su tipo MIME
        return response($file, Response::HTTP_OK)
            ->header('Link', '<link rel="shortcut icon" href="'.$rutaIcon.'" type="image/x-icon">')
            ->header('Content-Type', $mimeType);
    }
}
