<?php

namespace App\Livewire;

use App\Models\Archivo;
use App\Models\Etapa;
use App\Models\Imagen;
use App\Models\Paciente;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PacienteShow extends Component
{
    use WithFileUploads;

    public $paciente, $tratamientosAll, $tratamientos,$pacienteId;
    public $showModal = false;
    public $showTratamientoModal = false;
    public $files = [];
    public $uploadType = 'imagenes'; // Default to 'imagenes'
    public $selectedTratamiento;
    public $newTratamiento, $fecha;
    public $isCreatingNew = false;

    protected $rules = [
        'newTratamiento' => 'required|string|max:255',
    ];

    public function mount($id)
    {
        $this->pacienteId = $id;
        $this->paciente = Paciente::findOrFail($id);
        $this->tratamientosAll = Tratamiento::all();
        $this->loadTratamientos();
        // dd($this->paciente->tratamientos);
    }

    protected function loadTratamientos()
    {
        $this->tratamientos = PacienteTrat::with('tratamiento')
        ->where('paciente_id', $this->paciente->id )
        ->orderBy('created_at', 'desc')
        ->get();
        // dd($this->tratamientos);
    }

    public function render()
    {
        // Obtener las etapas relacionadas con los tratamientos del paciente
        $etapasIds = Etapa::whereIn('trat_id', $this->tratamientos->pluck('id'))
            ->pluck('id');

        // Cargar imágenes y archivos asociados a esas etapas
        $imagenes = Imagen::whereIn('etapa_id', $etapasIds)->get();
        $archivos = Archivo::whereIn('etapa_id', $etapasIds)->get();

        return view('livewire.paciente-show', [
            'paciente' => $this->paciente,
            'imagenes' => $imagenes,
            'archivos' => $archivos,
            'tratamientos' => $this->tratamientos,
            'tratamientosAll' => $this->tratamientosAll
        ]);
    }

    public function atras() {
        return redirect()->route('clinica.pacientes');
    }

    // cambiar revisión
    public function revision(){
        $paciente = Paciente::findOrFail($this->paciente->id);

        $paciente->revision = $this->fecha;

        $paciente->save();
        $this->dispatch('revision');
        return redirect()->route('pacientes-show',$this->paciente->id);

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
        $tratEtapa = $this->paciente->tratamientos()
        ->where('trat_id', $this->selectedTratamiento)
        ->first();
        dd($tratEtapa);
        $tratEtapaId = $tratEtapa->pivot->id; // Obtener el ID del pivote

        foreach ($this->files as $file) {
            // Guardar archivo en la carpeta correspondiente
            $path = $file->store($folder, 'public');

            // Crear registro en la base de datos
            if ($this->uploadType == 'imagenes') {
                Imagen::create([
                    'etapa_id' => $tratEtapaId,
                    'ruta' => $path,
                ]);
            } else {
                Archivo::create([
                    'etapa_id' => $tratEtapaId,
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

        if ($this->isCreatingNew) {
            $this->validate();

            // Save new tratamiento
            $tratamiento = Tratamiento::create([
                'name' => $this->newTratamiento,
            ]);

            // Refresh list of tratamientos
            $this->tratamientosAll = Tratamiento::all();
            $this->reset('newTratamiento');
            // Select newly created tratamiento
            $tratamientoId = $tratamiento->id;
        }

        // Save relation to the patient
        PacienteTrat::updateOrCreate(
            [
                'trat_id' => $tratamientoId,
                'paciente_id' => $this->paciente->id,
            ]
        );
        // Crear la etapa inicial
        $tratamiento = Tratamiento::findOrFail($tratamientoId);
        $tratamiento->etapas()->create([
            'trat_id' => $tratamiento,
            'name' => 'Inicio',
            'status' => 'Set Up'
        ]);

        // Recargar los tratamientos
        $this->loadTratamientos();

        $this->closeTratamientosModal();
    }
}
