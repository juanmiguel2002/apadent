<?php

namespace App\Livewire;

use App\Models\Archivo;
use Livewire\Component;

class ImagenesEtapa extends Component
{
    public $archivos, $etapa, $paciente;

    public function mount($etapa, $paciente){
        $this->etapa = $etapa;
        $this->paciente = $paciente;
        $this->archivos = Archivo::where('etapa_id', $this->etapa->id)
            ->whereIn('tipo', ['jpg', 'jpeg', 'png'])
            ->get();
    }

    public function render()
    {
        return view('livewire.imagenes-etapa');
    }
}
