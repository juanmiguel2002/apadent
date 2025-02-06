<?php

namespace App\Http\Controllers\Pacientes;

use App\Http\Controllers\Controller;
use App\Models\Paciente;

class PacienteShowController extends Controller
{
    //
    public function show($id)
    {
        $paciente = Paciente::findOrFail($id);
        return view('pacientes.paciente-show', ['id' => $id, 'paciente' => $paciente]);
    }
}
