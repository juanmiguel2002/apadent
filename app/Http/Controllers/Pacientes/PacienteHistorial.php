<?php

namespace App\Http\Controllers\Pacientes;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\PacienteTrat;

class PacienteHistorial extends Controller
{
    public function index($id, $tratId = null)
    {
        // Obtener el paciente por su ID
        $paciente = Paciente::with('tratamientos')->findOrFail($id);

        // Si se pasa un tratamiento, buscar el tratamiento específico del paciente
        $tratamiento = null;
        if ($tratId) {
            $tratamiento = PacienteTrat::where('paciente_id', $paciente->id)
                                    ->where('trat_id', $tratId)
                                    ->with('tratamiento')
                                    ->first();
        }else{
            // Si no se pasa un tratamiento
            $tratamiento = PacienteTrat::where('paciente_id', $paciente->id)->with('tratamiento')->get();
        }

        // Pasar el paciente y el tratamiento a la vista
        return view('pacientes.paciente-historial', [
            'paciente' => $paciente,
            'tratamiento' => $tratamiento, // Puede ser null si no se pasa un tratId
            'tratId' => $tratId, // Este se usará en el componente Livewire
        ]);
    }

}
