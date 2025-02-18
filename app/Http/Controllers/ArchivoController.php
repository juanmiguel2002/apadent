<?php

namespace App\Http\Controllers;

use App\Models\Factura;
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

    // public function delete(Factura $factura){
    //     // Verificar si el archivo existe
    //     if (Storage::disk('clinicas')->exists($factura->ruta)) {
    //         // Eliminar el archivo
    //         Storage::disk('clinicas')->delete($factura->ruta);
    //         Factura::destroy($factura->id);

    //         // Retornar un mensaje de éxito
    //         return response()->json(['message' => 'Archivo eliminado correctamente.'], 200);
    //     }

    //     // Si el archivo no existe, devolver un error
    //     return abort(404, 'Archivo no encontrado.');
    // }
}
