<?php

namespace App\Livewire\Pacientes;

use App\Models\Archivo;
use Livewire\Component;

class ImagenesEtapa extends Component
{
    public $archivos, $etapa, $paciente, $tratamiento;

    public function mount($etapa, $paciente, $tipo){
        $this->etapa = $etapa;
        $this->paciente = $paciente;
        $this->archivos = Archivo::where('etapa_id', $this->etapa->id)
            ->whereIn('extension', ['jpg', 'jpeg', 'png'])
            ->where('tipo', $tipo)
            ->get();
        $this->tratamiento = $this->etapa->tratamiento;
        // $this->tratamiento;
    }

    public function render()
    {
        return view('livewire.pacientes.imagenes-etapa');
    }
}
