<?php

namespace App\Livewire;

use App\Models\Tratamiento;
use Livewire\Component;
use Livewire\WithPagination;

class Tratamientos extends Component
{
    use WithPagination;
    public $tratamientos, $showModal, $isEditing, $trat_id;
    public $name, $descripcion;

    public function mount(){
        $this->tratamientos = Tratamiento::get();
    }

    public function render()
    {
        return view('livewire.tratamientos');
    }

    public function showCreateModal($tratamientoId = null)
    {
        $this->reset([
            'name', 'descripcion'
        ]);
        $this->trat_id = $tratamientoId;
        if ($tratamientoId) {
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

        if ($this->isEditing) {
            $tratamiento = Tratamiento::findOrFail($this->trat_id);
            $tratamiento->name = $this->name;
            $tratamiento->descripcion = $this->descripcion;
            $tratamiento->save();
        } else {
            $trat = Tratamiento::create([
                'name' => $this->name,
                'descripcion' => $this->descripcion
            ]);

            $trat->etapas()->sync([1]);
            $this->dispatch('tratamiento');  // Emite un evento para que otros componentes puedan escuchar
        }
        $this->reset([
            'name', 'descripcion'
        ]);
        $this->close();
        $this->resetPage();
    }

    public function delete($trat_id)
    {
        $tratamiento = Tratamiento::findOrFail($trat_id);
        $tratamiento->delete();

        // Emitir un mensaje de éxito
        $this->dispatch('tratamientoEliminado', 'El tratamiento ha sido eliminado correctamente.');
        $this->resetPage();

    }

    public function close()
    {
        $this->showModal = false;
    }
}
