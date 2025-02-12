<?php

namespace App\Livewire\Carpeta;

use App\Models\Carpeta;
use Livewire\Component;

class CarpetaShow extends Component
{
    public $nombre, $carpeta, $carpeta_id;
    public $showModal = false;
    public $isEditing = false;
    public $subcarpetas;

    public function mount($id) {
        // $this->carpeta_id = $id;k
        $this->carpeta = Carpeta::findOrFail($id);
        $this->subcarpetas = $this->carpeta->carpetasHija;
    }
    public function render()
    {
        return view('livewire.carpeta.carpeta-show');
    }

    public function showCreateModal() {
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function showEditModal($id) {

        $this->showModal = true;
        $this->isEditing = true;
        $this->nombre = Carpeta::findOrFail($id)->nombre;
    }

    public function save() {
        // Aquí se guarda la información en la base de datos
        $this->validate([
            'nombre' => 'required'
        ]);

        Carpeta::updateOrCreate(['id' => $this->carpeta_id],[
            'nombre' => $this->nombre,
            'carpeta_id' => $this->carpeta->id
        ]);

        $this->close();
        $this->dispatch('carpetaCreated', $this->carpeta_id ? 'Subcarpeta Actualizada.' : 'Subcarpeta Creada');
        $this->mount($this->carpeta->id);
    }

    public function close() {
        $this->showModal = false;
    }
}
