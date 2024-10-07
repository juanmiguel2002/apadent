<?php

namespace App\Http\Controllers;

use App\Models\Paciente;

class PacienteShowController extends Controller
{
    //
    public function show($id)
    {
        $paciente = Paciente::findOrFail($id);
        return view('paciente-show', ['id' => $id, 'paciente' => $paciente]);
    }
}
