<?php

namespace App\Livewire;

use App\Models\Archivos;
use Livewire\Component;

class ImagenesEtapa extends Component
{
    public $archivos, $etapa, $paciente;

    public function mount($etapa, $paciente){
        $this->etapa = $etapa;
        $this->paciente = $paciente;
        $this->archivos = Archivos::where('paciente_etapa_id', $this->etapa->id)
        ->where('paciente_id', $paciente->id)->get();
        // dd($this->archivos, $this->etapa, $this->paciente);
    }

    public function render()
    {
        return view('livewire.imagenes-etapa');
    }
}
