<?php

namespace App\Livewire\Pacientes;

use App\Models\Etapa;
use App\Models\Paciente;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;
use Livewire\Component;

class NuevaDocumentacion extends Component
{

    public $pacienteId, $tratamientoId, $tratamientos, $tratamiento;
    public $documents;
    public $documentacion_id, $documentacion, $mensaje, $selectedEtapa;
    public $descripcion;
    public $etapas;
    protected $listeners = ['abrirModalDocumentacion' => 'showDocumentacionModal'];

    protected $rules = [
        'selectedEtapa' => 'required|exists:etapas,id',
        'documentacion' => 'nullable|file|max:2048',
        'mensaje' => 'nullable|string|max:500',
    ];

    // public function mount($pacienteId, $tratamientoId = null)
    // {
    //     $this->pacienteId = $pacienteId;
    //     $this->tratamientoId = $tratamientoId;

    //     // Si no se pasa tratamientoId, cargar los tratamientos del paciente
    //     if (!$this->tratamientoId) {
    //         $this->tratamientos = PacienteTrat::where('paciente_id', $this->pacienteId)
    //             ->with('tratamiento') // Asegúrate de que existe la relación en el modelo
    //             ->get();
    //     }
    //     $this->tratamiento = PacienteTrat::where('paciente_id',$this->pacienteId);
    //     $this->loadEtapas();
    // }
    public function loadEtapas()
    {
        $this->etapas = Etapa::whereHas('fase', function ($query) {
            $query->where('trat_id', $this->tratamientoId);
        })->where('paciente_id', $this->pacienteId)->get();
    }


    public function showDocumentacionModal($pacienteId, $tratamientoId = null){
        $this->documents = true;
        $this->pacienteId = $pacienteId;
        $this->tratamientoId = $tratamientoId;

        // Si no se pasa tratamientoId, cargar los tratamientos del paciente
        if (!$this->tratamientoId) {
            $this->tratamientos = PacienteTrat::where('paciente_id', $this->pacienteId)
                ->with('tratamiento') // Asegúrate de que existe la relación en el modelo
                ->get();
            $this->tratamiento = null; // Asegura que la variable existe
        } else {
            $this->tratamiento = Tratamiento::find($this->tratamientoId);
        }
        logger($this->tratamiento);
        $this->loadEtapas();
    }

    public function saveDocumentacion($tratId, $etapaId){

    }
    public function closeModal()
    {
        $this->reset(['documentacion', 'mensaje', 'selectedEtapa']);
        $this->documents = false;
    }

    public function render()
    {
        return view('livewire.pacientes.nueva-documentacion');
    }
}
