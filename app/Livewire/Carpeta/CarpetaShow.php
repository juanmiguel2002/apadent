<?php

namespace App\Livewire\Carpeta;

use App\Models\Carpeta;
use App\Models\Clinica;
use App\Models\Factura;
use Livewire\Component;

class CarpetaShow extends Component
{
    public $nombre, $carpeta, $carpeta_id;
    public $showModal = false;
    public $isEditing = false;
    public $subcarpetas;
    public $archivos;
    public $facturas;

    public function mount($id)
    {
        // Obtener la carpeta específica
        $this->carpeta = Carpeta::findOrFail($id);

        // Obtener la clínica a la que pertenece la carpeta
        $clinica = Clinica::whereHas('carpetas', function ($query) use ($id) {
            $query->where('id', $id);
        })->firstOrFail();

        // Cargar subcarpetas y archivos de la carpeta actual
        $this->subcarpetas = $this->carpeta->carpetasHija;
        $this->archivos = $this->carpeta->archivos ?? collect();

        // Obtener facturas dentro de la carpeta "facturas" de la clínica
        $this->facturas = Factura::where('clinica_id', $clinica->id)
                                ->where('carpeta_id', $this->carpeta->id)
                                ->get();
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
