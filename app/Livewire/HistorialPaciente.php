<?php

namespace App\Livewire;

use App\Models\Archivo;
use App\Models\Etapa;
use App\Models\Mensaje;
use App\Models\Paciente;
use App\Models\PacienteTrat;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class HistorialPaciente extends Component
{
    use WithFileUploads;

    public $paciente;
    public $tratamientoId   ;
    public $tratamientos;
    public $etapas;
    public $mensajes;
    public $selectedTratamiento;
    public $mensaje = '';
    public $isDoctor;
    public $mostrar = false;

    public $statuses = [
        'En proceso' => 'bg-green-600',
        'Pausado' => 'bg-blue-600',
        'Finalizado' => 'bg-red-600',
        'Set Up' => 'bg-yellow-600'
    ];

    public function mount($paciente, $tratamientos)
    {
        $this->paciente = $paciente;
        $this->tratamientos = $tratamientos;
        $this->selectedTratamiento = $tratamientos->first()->id ?? null;
        $this->loadEtapas();
    }

    public function loadEtapas()
    {
        if ($this->selectedTratamiento) {
            $this->etapas = Etapa::where('trat_id', $this->selectedTratamiento)->get();
        } else {
            $this->etapas = collect();
        }
    }

    public function updatedSelectedTratamiento()
    {
        $this->loadEtapas();
    }
    
    public function render()
    {
        return view('livewire.historial-paciente');
    }

    public function enviarMensaje($etapaId)
    {
        if ($this->mensaje) {
            Mensaje::create([
                'etapa_id' => $etapaId,
                'user_id' => Auth::id(),
                'mensaje' => $this->mensaje,
            ]);
            $this->mensaje = '';
            $this->updatedSelectedTratamiento($this->selectedTratamiento); // Reload messages
        }
    }

    public function nuevaEtapa()
    {
        // Obtener la última etapa para el paciente dado
        $ultimaEtapa = PacienteTrat::where('paciente_id', $this->pacienteId)
            ->where('trat_id', $this->tratamientoId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Determinar el número de la nueva etapa
        $j = 1;
        if ($ultimaEtapa) {
            // Extraer el número de la última etapa y calcular el siguiente
            if (preg_match('/Etapa (\d+)/', $ultimaEtapa->name, $matches)) {
                $i = (int)$matches[1];
                $j = $i + 1;
            }
        }

        // Crear una nueva etapa
        $etapa = new Etapa;
        $etapa->name = "Etapa " . $j;
        $etapa->trat_id = $this->tratamientoId;
        $etapa->status = "Set Up";
        $etapa->save();

        // Enviar un correo al usuario del paciente
        // $paciente = Paciente::findOrFail($id);
        // $mail = User::findOrFail($paciente->user_id);
        // Mail::to($mail->email)->send(new NuevaEtapa($etapa->name, $paciente->name));

        // Emitir un evento para actualizar la vista
        $this->dispatch('etapa');
    }

    public function estado($tratId, $newStatus)
    {
        $validStatuses = ['En proceso', 'Pausado', 'Finalizado', 'Set Up']; // Asegúrate de que estos sean los mismos valores en tu enum

        // Validar que el nuevo estado sea válido
        if (!in_array($newStatus, $validStatuses)) {
            // Manejar el error de manera apropiada
            return;
        }

        // Encontrar la etapa correspondiente y actualizar el estado
        $etapa = Etapa::where('trat_id', $tratId)->first();

        if ($etapa) {
            $etapa->status = $newStatus;
            $etapa->save();

            // Actualizar el estado localmente (en caso de ser necesario)
            $this->paciente->tratamiento_status = $newStatus;
            $this->mostrar = false; // Cerrar el menú
        }
    }

    public function toggleMenu()
    {
        $this->mostrar = !$this->mostrar;
    }

}
