<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Clinica;
use App\Models\Tratamiento;
use App\Mail\NewTratamiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class Tratamientos extends Component
{
    public $tratamientos, $trat_id;
    public $showModal = false, $isEditing = false;
    public $name, $descripcion;

    public function mount(){
        // Filtrar los tratamientos por la clinica del usuario logueado
        $clinicaId = Auth::user()->clinicas->first()->id;
        $clinica = Clinica::find($clinicaId);
        $this->tratamientos = $clinica->tratamientos;
    }

    public function render()
    {
        return view('livewire.tratamientos'); //
    }

    public function showCreateModal($tratamientoId = null)
    {
        $this->reset([
            'name', 'descripcion'
        ]);
        $this->trat_id = $tratamientoId;
        if ($this->trat_id) {
            $tratamiento = Tratamiento::findOrFail($this->trat_id);
            $this->name = $tratamiento->name;
            $this->descripcion = $tratamiento->descripcion;
            $this->isEditing = true;
        }else{
            $this->isEditing = false;
        }
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'string|required|max:50',
            'descripcion' => 'string|required|max:255'
        ]);
        // Obtener la clinica del usuario logueado
        $clinica = Auth::user()->clinicas->first();

        $tratamiento = Tratamiento::updateOrCreate(['id' => $this->trat_id],[
            'name' => $this->name,
            'descripcion' => $this->descripcion,
        ]);
        $clinica->tratamientos()->attach($tratamiento->id); // Agrega un tratamiento a una clínica
        $this->dispatch('tratamiento', $this->trat_id ? 'Tratamiento Actualizado.' : 'Tratamiento Creado');

        Mail::to($clinica->email)->send(new NewTratamiento($tratamiento, $this->trat_id ? 'Tratamiento actualizado.' : 'Tratamiento Creado',$this->trat_id ? true : false));

        $this->close();
        $this->mount();
    }

    public function close()
    {
        $this->showModal = false;
    }
}
