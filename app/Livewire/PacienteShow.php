<?php

namespace App\Livewire;

use App\Models\Archivo;
use App\Models\Imagen;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Models\TratEtapa;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PacienteShow extends Component
{
    use WithFileUploads;

    public $paciente, $tratamientos;
    public $showModal = false;
    public $showTratamientoModal = false;
    public $files = [];
    public $uploadType = 'imagenes'; // Default to 'imagenes'
    public $selectedTratamiento;
    public $newTratamiento;
    public $isCreatingNew = false;

    protected $rules = [
        'newTratamiento' => 'required|string|max:255',
    ];

    public function mount($id)
    {
        $this->paciente = Paciente::findOrFail($id);
        $this->tratamientos = Tratamiento::all();
    }

    public function render()
    {
        $imagenes = Imagen::whereHas('tratEtapa', function ($query) {
            $query->where('paciente_id', $this->paciente->id);
        })->get();

        $archivos = Archivo::whereHas('tratEtapa', function ($query) {
            $query->where('paciente_id', $this->paciente->id);
        })->get();

        return view('livewire.paciente-show', [
            'imagenes' => $imagenes,
            'archivos' => $archivos,
            'tratamientos' => $this->tratamientos,
        ]);
    }

    public function atras() {
        return redirect()->route('clinica.pacientes');
    }

    // GESTIÓN DE IMAGENES Y ARCHIVOS
    public function showImagenes()
    {
        $this->uploadType = 'imagenes';
        $this->showModal = true;
    }

    public function showCbct()
    {
        $this->uploadType = 'cbct';
        $this->showModal = true;
    }

    public function close()
    {
        $this->showModal = false;
    }

    public function save()
    {
        // Definir la carpeta de almacenamiento basada en el tipo de archivo
        $folder = $this->uploadType == 'imagenes' ? 'imagenPaciente' : 'pacienteCbct';

        // Obtener el primer tratamiento asociado con el paciente
        $tratEtapa = $this->paciente->tratEtapas()
        ->where('tratamiento_id', $this->selectedTratamiento)
        ->first();
        dd($tratEtapa);
        $tratEtapaId = $tratEtapa->pivot->id; // Obtener el ID del pivote

        foreach ($this->files as $file) {
            // Guardar archivo en la carpeta correspondiente
            $path = $file->store($folder, 'public');

            // Crear registro en la base de datos
            if ($this->uploadType == 'imagenes') {
                Imagen::create([
                    'trat_etapa_id' => $tratEtapaId,
                    'ruta' => $path,
                ]);
            } else {
                Archivo::create([
                    'trat_etapa_id' => $tratEtapaId,
                    'ruta' => $path,
                ]);
            }
        }

        // session()->flash('message', 'Archivos subidos exitosamente.');
        $this->files = [];
        $this->close();
    }

    public function delete($fileName)
    {
        $folder = $this->uploadType == 'imagenes' ? 'imagenPaciente' : 'pacienteCbct';
        Storage::disk('public')->delete("$folder/$fileName");

        if ($this->uploadType == 'imagenes') {
            Imagen::where('ruta', "$folder/$fileName")->delete();
        } else {
            Archivo::where('ruta', "$folder/$fileName")->delete();
        }

        $this->emitSelf('render');
    }

    // GESTIÓN NEW TRATAMIENTO
    public function showTratamientosModal()
    {
        $this->showTratamientoModal = true;
        $this->isCreatingNew = false; // Ensure we're not creating a new treatment initially
    }

    public function closeTratamientosModal()
    {
        $this->showTratamientoModal = false;
        $this->reset(['selectedTratamiento', 'newTratamiento']);
    }

    public function selectTratamiento($tratamientoId)
    {
        $this->selectedTratamiento = $tratamientoId;
        $this->isCreatingNew = false;
    }

    public function createNewTratamiento()
    {
        $this->isCreatingNew = true;
        $this->selectedTratamiento = null;
    }

    public function saveTratamiento()
    {
        $tratamientoId = $this->selectedTratamiento;
        // dd($tratamientoId);

        if ($this->isCreatingNew) {
            $this->validate();

            // Save new tratamiento
            $tratamiento = Tratamiento::create([
                'name' => $this->newTratamiento,
            ]);

            // Refresh list of tratamientos
            $this->tratamientos = Tratamiento::all();
            $this->reset('newTratamiento');
            // Select newly created tratamiento
            $tratamientoId = $tratamiento->id;
        }

        // Save relation to the patient
        TratEtapa::updateOrCreate(
            [
                'tratamiento_id' => $tratamientoId,
                'paciente_id' => $this->paciente->id,
                'status' => 'Set Up'
            ]
        );

        $this->closeTratamientosModal();
    }
}
