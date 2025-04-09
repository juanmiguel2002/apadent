<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Clinica;
use App\Models\Paciente;
use Illuminate\Support\Facades\Storage;

class PacientesAdmin extends Controller
{
    public function index()
    {
        return view('admin.pacientesAdmin');
    }

    public function destroy($id)
    {
        // Buscar el paciente
        $paciente = Paciente::findOrFail($id);

        // Obtener la clínica asociada
        $clinica = Clinica::findOrFail($paciente->clinica_id);

        // Normalizar nombres
        $nombreClinica = preg_replace('/\s+/', '_', trim($clinica->name));
        $primerApellido = strtok($paciente->apellidos, " ");
        $nombrePaciente = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $primerApellido . ' ' . $paciente->num_paciente));
        $nombrePacienteBBDD = $paciente->name . ' ' . $primerApellido . '_' . $paciente->num_paciente;

        // Construir la ruta de la carpeta del paciente en almacenamiento
        $rutaPaciente = "{$nombreClinica}/pacientes/{$nombrePaciente}";

        // Eliminar archivos dentro de carpetas que pertenecen a este paciente
        $carpetasPaciente = Carpeta::where('clinica_id', $clinica->id)
                                ->where('nombre', $nombrePacienteBBDD)
                                ->pluck('id');

        Archivo::whereIn('carpeta_id', $carpetasPaciente)->delete();

        // Eliminar solo la carpeta del paciente en la base de datos, sin tocar otras carpetas de la clínica
        Carpeta::where('clinica_id', $clinica->id)
            ->where('nombre', $nombrePacienteBBDD)
            ->delete();

        // Eliminar el paciente de la base de datos
        $paciente->delete();

        // Eliminar solo la carpeta del paciente y su contenido
        if (Storage::disk('clinicas')->exists($rutaPaciente)) {
            Storage::disk('clinicas')->deleteDirectory($rutaPaciente);
        }
        else {
            return redirect()->route('admin.pacientes')->with('error', 'No se encontró la carpeta del paciente.');
        }

        if (Storage::disk('public')->exists($rutaPaciente)) {
            Storage::disk('public')->deleteDirectory($rutaPaciente);
        }
        else {
            return redirect()->route('admin.pacientes')->with('error', 'No se encontró la carpeta publica del paciente.');
        }

        return redirect()->route('admin.pacientes')->with('success', 'Paciente eliminado correctamente.');
    }
}
