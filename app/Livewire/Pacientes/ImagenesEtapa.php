<?php

namespace App\Livewire\Pacientes;

use App\Models\Archivo;
use Livewire\Component;

class ImagenesEtapa extends Component
{
    public $archivos, $etapa, $paciente, $tratamiento;

    public function mount($etapa = null, $paciente, $tipo){
        $this->etapa = $etapa;
        $this->paciente = $paciente;
        if($this->etapa === null) {
            $this->archivos = Archivo::where('etapa_id', null)
            ->whereIn('extension', ['jpg', 'jpeg', 'png'])
            ->where('tipo', $tipo)
            ->get();
        }else {
            $this->archivos = Archivo::where('etapa_id', $this->etapa->id)
                ->whereIn('extension', ['jpg', 'jpeg', 'png'])
                ->where('tipo', $tipo)
                ->get();
            $this->tratamiento = $this->etapa->tratamiento;
        }
    }

    public function render()
    {
        return view('livewire.pacientes.imagenes-etapa');
    }
}
