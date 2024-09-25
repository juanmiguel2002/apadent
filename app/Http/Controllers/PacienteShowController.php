<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PacienteShowController extends Controller
{
    //
    public function show($id)
    {
        $paciente = Paciente::findOrFail($id);
        return view('paciente-show', ['id' => $id, 'paciente' => $paciente]);
    }

    public function mostrarImagen($filename)
    {
        // Verificar que el usuario tiene rol "admin" o "doctor"
        if (!Auth::user()->hasRole(['doctor'])) {
            abort(403, 'No tienes permisos para ver esta imagen.');
        }

        // Recuperar la imagen del almacenamiento privado
        $file = Storage::disk('clinicas')->get($filename);

        // Devolver la imagen como una respuesta
        return response($file, 200)->header('Content-Type', Storage::disk('clinicas')->mimeType($filename));
    }
}
