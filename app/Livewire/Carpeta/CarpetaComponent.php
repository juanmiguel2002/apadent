<?php

namespace App\Livewire\Carpeta;

use App\Models\Carpeta;
use App\Models\Clinica;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class CarpetaComponent extends Component
{
    public $nombre, $carpetas, $carpeta_id;
    public $carpeta;
    public $showModal = false;
    public $isEditing = false;
    public $newName;

    public function mount() {
        $this->carpetas = Carpeta::where('carpeta_id', null)->get();
    }

    public function render()
    {
        return view('livewire.carpeta.index');
    }

    public function showEditModal($id) {

        $this->carpeta = Carpeta::findOrFail($id);
        $this->carpeta_id = $id;
        $this->newName = $this->carpeta->nombre;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'newName' => 'required|string|max:255',
        ]);

        if ($this->isEditing) {
            $clinica = Clinica::findOrFail($this->carpeta->clinica_id);

            // Construir la ruta actual basada en la jerarquía
            // $rutaActual = 'clinicas/' . $this->obtenerRutaCompleta($this->carpeta);
            // $nuevaRuta = 'clinicas/' . $this->nombre;
            // // dd('Ruta actual: '.$rutaActual, 'Ruta nueva: '.$nuevaRuta);

            // // Renombrar la carpeta en el sistema de archivos
            // if (Storage::disk('clinicas')->exists($rutaActual)) {
            //     Storage::disk('clinicas')->move($rutaActual, $nuevaRuta);
            //     $this->actualizarRutasSubcarpetas($this->carpeta, $rutaActual, $nuevaRuta);
            // }

            $this->carpeta->update(['nombre' => $this->newName]);
            $clinica->update(['name' => $this->newName]);
        }

        $this->close();
        $this->mount();
    }

    public function rename()
    {
        $carpeta = Carpeta::findOrFail($this->carpetaId);
        $clinica = Clinica::findOrFail($this->carpeta->clinica_id);

        $oldPath = $this->getPath($carpeta);
        $newPath = str_replace("/{$carpeta->nombre}/", "/{$this->newName}/", $oldPath);

        // Verificar si existe la carpeta original
        if (!Storage::disk('clinicas')->exists($oldPath)) {
            session()->flash('error', 'Carpeta no encontrada.');
            return;
        }

        // Renombrar en el almacenamiento
        Storage::disk('clinicas')->move($oldPath, $newPath);

        // Actualizar en la base de datos
        $carpeta->update(['nombre' => $this->newName]);
        $clinica->update(['name' => $this->nombre]);
        $this->close();
        $this->mount();
        $this->dispatch('carpetaEdit', 'message');

        session()->flash('message', 'Carpeta renombrada correctamente.');
    }

    private function getPath($carpeta)
    {
        $path = "clinicas/{$this->clinicId}/";

        // Si es una subcarpeta, construir ruta completa
        while ($carpeta) {
            $path .= "{$carpeta->nombre}/";
            $carpeta = $carpeta->parent;
        }

        return rtrim($path, '/');
    }

    public function close()
    {
        $this->reset(['showModal', 'isEditing', 'nombre', 'carpeta_id']);
    }
}
