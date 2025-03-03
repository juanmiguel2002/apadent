<?php

namespace App\Livewire\Carpeta;

use App\Models\Carpeta;
use App\Models\Clinica;
use App\Models\Factura;
use App\Models\Tratamiento;
use Livewire\Component;
use Livewire\WithFileUploads;

class CarpetaShow extends Component
{
    use WithFileUploads;

    public $files = [];
    public $tipo;
    public $nombre, $carpeta, $carpeta_id;
    public $showModal = false;
    public $isEditing = false;
    public $showArchivos = false;

    public $subcarpetas;
    public $archivos;
    public $facturas;
    public $tratamientos;
    public $paciente;

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
        // Obtener tratamientos a partir de las etapas de los archivos
        $this->tratamientos = Tratamiento::whereHas('etapas', function ($query) {
            $query->whereIn('id', $this->archivos->pluck('etapa_id')->filter());
        })->get();
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

    public function showCreateArchivos() {
        $this->showArchivos = true;
    }

    public function saveArchivos() {
        $this->validate([
            'files.*' => 'file|max:2048', // Máximo 2MB por archivo
        ]);

        $paths = [];
        foreach ($this->files as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('facturas', $filename, 'clinicas');
            $paths[] = "/storage/$path";
        }

        // Emitimos evento para notificar a otros componentes si es necesario
        $this->emit('fileUploaded', $paths);

        // Reseteamos los archivos
        $this->reset('files');
    }

    public function close() {
        if($this->showModal){
            $this->showModal = false;
        }else{
            $this->showArchivos = false;
        }
    }
}
