<?php

namespace App\Livewire\Carpeta;

use App\Models\Carpeta;
use App\Models\Clinica;
use App\Models\Factura;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CarpetaShow extends Component
{
    use WithFileUploads, WithPagination;

    public $files = [];
    public $clinica;
    public $nombre, $carpeta, $carpeta_id;
    public $showModal = false;
    public $isEditing = false;
    public $showArchivos = false;

    public $subcarpetas;
    // public $archivos;
    public $facturas;
    public $tratamientos;
    public $paciente;
    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount($id)
    {
        // Obtener la carpeta específica
        $this->carpeta = Carpeta::findOrFail($id);

        // Obtener la clínica a la que pertenece la carpeta
        $this->clinica = Clinica::whereHas('carpetas', function ($query) use ($id) {
            $query->where('id', $id);
        })->firstOrFail();

        // Obtener facturas dentro de la carpeta "facturas" de la clínica
        $this->facturas = Factura::where('clinica_id', $this->clinica->id)
                                ->where('carpeta_id', $this->carpeta->id)
                                ->get();
    }

    // Método computado que devuelve los archivos paginados
    public function getArchivosProperty()
    {
        return $this->carpeta->archivos()->paginate(10); // 10 archivos por página
    }

    public function render()
    {
        $this->subcarpetas = $this->carpeta->carpetasHija()
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', "%{$this->search}%"); // Filtrar por nombre de carpeta
            })->get();

        return view('livewire.carpeta.carpeta-show',[
            'archivos' => $this->archivos // Livewire lo detecta automáticamente
        ]);
    }

    // public function showCreateModal() {
    //     $this->showModal = true;
    //     $this->isEditing = false;
    // }

    public function showEditModal($id) {
        $this->carpeta_id = $id;
        $this->showModal = true;
        $this->isEditing = true;
        $this->nombre = Carpeta::findOrFail($this->carpeta_id)->nombre;
    }

    public function save() {
        // Validar que el nombre sea obligatorio
        $this->validate([
            'nombre' => 'required'
        ]);
        $subcarpeta = Carpeta::findOrFail($this->carpeta_id);

        // Actualizar solo el nombre de la carpeta
        $subcarpeta->update([
            'nombre' => $this->nombre,
        ]);

        // Cerrar el modal y emitir un mensaje de éxito
        $this->close();
        $this->dispatch('carpetaUpdated', 'Nombre de la carpeta actualizado correctamente.');
        $this->mount($subcarpeta->id);
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
