<?php

namespace App\Livewire\Carpeta;

use App\Models\Carpeta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Livewire\Component;

class CarpetaComponent extends Component
{
    public $nombre, $carpetas, $carpeta_id;
    public $showModal = false;
    public $isEditing = false;

    public function mount() {
        $this->carpetas = Carpeta::where('carpeta_id', null)->get();
    }

    public function render()
    {
        return view('livewire.carpeta.index');
    }

    // public function showCreateModal() {
    //     $this->showModal = true;
    //     $this->isEditing = false;
    // }

    // public function showEditModal($id) {
    //     $this->carpeta_id = $id;
    //     $this->showModal = true;
    //     $this->isEditing = true;
    //     $this->nombre = Carpeta::findOrFail($id)->nombre;
    // }


    // public function save() {
    //     // Aquí se guarda la información en la base de datos
    //     $this->validate([
    //         'nombre' => 'required'
    //     ]);

    //     // Buscar la carpeta actual para obtener el nombre anterior
    //     $carpeta = Carpeta::find($this->carpeta_id);
    //     $nombreAnterior = $carpeta ? $carpeta->nombre : null;

    //     // Actualizar o crear la carpeta
    //     $carpeta = Carpeta::updateOrCreate(['id' => $this->carpeta_id], [
    //         'nombre' => $this->nombre
    //     ]);


    //     $this->close();
    //     $this->dispatch('carpetaCreated', $this->carpeta_id ? 'Carpeta Actualizada.' : 'Carpeta Creada');
    //     $this->mount();
    // }

    // public function close() {
    //     $this->showModal = false;
    // }
}
