<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class ArchivoController extends Controller
{
    //
    public function archivo($filePath){
     // Verifica que el archivo exista en el sistema de almacenamiento
        if (!Storage::disk('clinicas')->exists($filePath)) {
            abort(404, 'Archivo no encontrado');
        }

        // Retorna la imagen desde el almacenamiento privado
        return Storage::disk('clinicas')->download($filePath);
    }
}
