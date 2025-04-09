<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Clinica;
use App\Models\Factura;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CarpetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.carpeta.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $carpeta = Carpeta::findOrFail($id);

        return view('admin.carpeta.show', compact( 'carpeta', 'id'));
    }

    public function destroy($id, $subcarpetaId = null)
    {
        // Buscar la carpeta principal
        $carpeta = Carpeta::findOrFail($id);
        $clinica = Clinica::findOrFail($carpeta->clinica_id);

        // Normalizar nombres para rutas
        $nombreClinica = preg_replace('/\s+/', '_', trim($clinica->name));
        $rutaClinica = "{$nombreClinica}";

        if ($subcarpetaId) {
            // Buscar la subcarpeta específica
            $subcarpeta = Carpeta::findOrFail($subcarpetaId);

            // Eliminar archivos dentro de la subcarpeta
            Archivo::where('carpeta_id', $subcarpetaId)->delete();

            // Eliminar la subcarpeta de la base de datos
            $subcarpeta->delete();
            $nombreSubcarpeta = preg_replace('/\s+/', '_', trim($subcarpeta->nombre));

            // Construir la ruta de la subcarpeta en almacenamiento
            $rutaSubcarpeta = "{$rutaClinica}/{$nombreSubcarpeta}";

            // Eliminar la carpeta física en el almacenamiento
            Storage::disk('clinicas')->deleteDirectory($rutaSubcarpeta);

            return redirect()->route('admin.clinica')->with('success', 'Subcarpeta eliminada correctamente.');

        }else {
            // Si no se pasa subcarpeta, eliminar toda la clínica
            $pacientes = Paciente::where('clinica_id', $clinica->id)->get();

            foreach ($pacientes as $paciente) {
                $primerApellido = strtok($paciente->apellidos, " ");
                $nombrePaciente = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $primerApellido . ' ' . $paciente->num_paciente));

                // Construir la ruta del paciente
                $rutaPaciente = "{$nombreClinica}/pacientes/{$nombrePaciente}";

                // Obtener carpetas del paciente y eliminar archivos
                $carpetasPaciente = Carpeta::where('clinica_id', $clinica->id)->where('nombre', $nombrePaciente)->pluck('id');
                Archivo::whereIn('carpeta_id', $carpetasPaciente)->delete();

                // Eliminar carpetas del paciente y su registro
                Carpeta::whereIn('id', $carpetasPaciente)->delete();
                $paciente->delete();

                // Eliminar carpetas físicas
                Storage::disk('clinicas')->deleteDirectory($rutaPaciente);
            }

            // Eliminar carpetas y archivos relacionados con la clínica
            Storage::disk('clinicas')->deleteDirectory($rutaClinica);
            Factura::where('clinica_id', $clinica->id)->delete();

            // Eliminar usuarios de la clínica
            $usuariosIds = DB::table('clinica_user')->where('clinica_id', $clinica->id)->pluck('user_id');
            if ($usuariosIds->isNotEmpty()) {
                DB::table('clinica_user')->where('clinica_id', $clinica->id)->delete();
                DB::table('model_has_roles')->whereIn('model_id', $usuariosIds)->delete();
                User::whereIn('id', $usuariosIds)->delete();
            }

            // Eliminar la clínica de la base de datos
            $clinica->delete();

            return redirect()->route('admin.archivos')->with('success', 'Clínica y todos sus datos eliminados correctamente.');
        }
    }
}
