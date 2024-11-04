<?php

namespace App\Livewire;

use App\Mail\NewTratamiento;
use App\Models\Tratamiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Tratamientos extends Component
{
    public $tratamientos, $trat_id;
    public $showModal = false, $isEditing = false;
    public $name, $descripcion;

    public function mount(){
        $this->tratamientos = Tratamiento::all();
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

        if ($this->isEditing) {
            $tratamiento = Tratamiento::findOrFail($this->trat_id);
            $tratamiento->name = $this->name;
            $tratamiento->descripcion = $this->descripcion;
            $tratamiento->save();
            $this->dispatch('tratamiento', 'Tratamiento Actualizado');  // Emite un evento para que otros componentes puedan escuchar
            Mail::to($clinica->email)->send(new NewTratamiento($tratamiento,'Actualizado Tratamiento',true));
        } else {
            $trat = Tratamiento::create([
                'name' => $this->name,
                'descripcion' => $this->descripcion
            ]);

            $trat->etapas()->sync([1]);
            $this->dispatch('tratamiento', 'Tratamiento Creado');  // Emite un evento para que otros componentes puedan escuchar
            Mail::to($clinica->email)->send(new NewTratamiento($trat,'Nuevo Tratamiento',false));
        }
        $this->close();
        $this->mount();
    }

    public function close()
    {
        $this->showModal = false;
    }
}
