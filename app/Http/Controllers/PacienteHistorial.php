<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\PacienteTrat;

class PacienteHistorial extends Controller
{
    public function index($id)
{
    $paciente = Paciente::findOrFail($id);
    $tratamientos = PacienteTrat::where('paciente_id', $paciente->id)->with('tratamiento')->get();

    return view('paciente-historial', [
        'paciente' => $paciente,
        'tratamientos' => $tratamientos,
    ]);
}

}
