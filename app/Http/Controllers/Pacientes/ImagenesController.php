<?php

namespace App\Http\Controllers\Pacientes;

use App\Http\Controllers\Controller;
use App\Models\Etapa;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagenesController extends Controller
{
    public function verImagenes(Request $request, $pacienteId, $etapaId)
    {
        $paciente = Paciente::findOrFail($pacienteId);
        $etapa = Etapa::findOrFail($etapaId);
        $tipo = $request->query('tipo'); // Valor por defecto 'imgetapa'

        return view('pacientes.imagenes', compact('etapa', 'paciente', 'tipo'));
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
