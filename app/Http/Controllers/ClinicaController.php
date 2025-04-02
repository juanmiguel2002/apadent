<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Clinica;
use App\Models\Factura;
use App\Models\Paciente;
use App\Models\User;
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

    public function destroy($id)
    {
        // Buscar la clínica
        $clinica = Clinica::findOrFail($id);

        $nombreClinica = preg_replace('/\s+/', '_', trim($clinica->name));
        $rutaClinica = "{$nombreClinica}";

        // Obtener todos los pacientes de la clínica
        $pacientes = Paciente::where('clinica_id', $clinica->id)->get();

        foreach ($pacientes as $paciente) {
            // Normalizar nombres

            $primerApellido = strtok($paciente->apellidos, " ");
            $nombrePaciente = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $primerApellido . ' ' . $paciente->num_paciente));

            // Construir la ruta de la carpeta del paciente en almacenamiento
            $rutaPaciente = "{$nombreClinica}/pacientes/{$nombrePaciente}";


            // Obtener carpetas del paciente en la clínica
            $carpetasPaciente = Carpeta::where('clinica_id', $clinica->id)
                                    ->where('nombre', $nombrePaciente)
                                    ->pluck('id');

            // Eliminar archivos relacionados con el paciente
            Archivo::whereIn('carpeta_id', $carpetasPaciente)->delete();

            // Eliminar carpetas del paciente
            Carpeta::whereIn('id', $carpetasPaciente)->delete();

            // Eliminar al paciente de la base de datos
            $paciente->delete();

            // Eliminar carpetas físicas del paciente
            Storage::disk('public')->deleteDirectory($rutaClinica);
        }

        // Eliminar la carpeta de la clínica
        Storage::disk('clinicas')->deleteDirectory($rutaClinica);
        // Eliminar facturas de la clínica (si aplica)
        Factura::where('clinica_id', $clinica->id)->delete();

        // Eliminar la clínica
        $clinica->delete();
        // Obtener y eliminar todos los usuarios de la clínica
        $usuarios = User::whereHas('clinicas', function ($query) use ($clinica) {
            $query->where('clinica_id', $clinica->id);
        })->get();

        foreach ($usuarios as $usuario) {
            $usuario->roles()->detach(); // Elimina roles de Spatie
            $usuario->delete(); // Elimina el usuario
        }

        return redirect()->route('admin.clinica')->with('success', 'Clínica y todos sus datos eliminados correctamente.');
    }
}
