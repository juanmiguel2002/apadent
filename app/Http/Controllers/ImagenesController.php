<?php

namespace App\Http\Controllers;

use App\Models\Etapa;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagenesController extends Controller
{
    public function verImagenes($pacienteId, $etapaId)
    {
        $paciente = Paciente::find($pacienteId);
        $etapa = Etapa::findOrFail($etapaId);

        return view('pacientes.imagenes', compact('etapa', 'paciente'));
    }

    // Método para servir las imágenes de forma protegida
    public function mostrarImagen($filePath)
    {
        // Verifica que el archivo exista en el sistema de almacenamiento
        if (!Storage::disk('clinicas')->exists($filePath)) {
            abort(404, 'Imagen no encontrada');
        }

        // Retorna la imagen desde el almacenamiento privado
        return response()->file(storage_path('app/clinicas/' . $filePath));
    }
}
