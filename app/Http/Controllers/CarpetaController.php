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

    public function destroy($id)
    {
        // Buscar la clínica
        $carpeta = Carpeta::findOrFail($id);
        $clinica = Clinica::findOrFail($carpeta->clinica_id);

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

        // Obtener y eliminar todos los usuarios de la clínica
        $usuariosIds = DB::table('clinica_user')->where('clinica_id', $clinica->id)->pluck('user_id');

        if ($usuariosIds->isNotEmpty()) {
            // Eliminar relaciones de la tabla intermedia clinica_user
            DB::table('clinica_user')->where('clinica_id', $clinica->id)->delete();

            // Eliminar roles y permisos de los usuarios
            DB::table('model_has_roles')->whereIn('model_id', $usuariosIds)->delete();

            // Eliminar los usuarios completamente de la tabla users
            User::whereIn('id', $usuariosIds)->delete();
        }

        // Eliminar la clínica
        $clinica->delete();

        return redirect()->route('admin.archivos')->with('success', 'Clínica y todos sus datos eliminados correctamente.');
    }

}
