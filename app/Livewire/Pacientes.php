<?php

namespace App\Livewire;

use App\Models\Archivo;
use App\Models\Imagen;
use App\Models\Paciente;
use App\Models\Tratamiento;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Pacientes extends Component
{
    use WithFileUploads, WithPagination;

    public $tratamientos, $clinica_id, $paciente_id;
    public $name, $email, $telefono, $num_paciente, $fecha_nacimiento, $revision, $observaciones, $obser_cbct, $odontograma_obser;
    public $showModal = false;
    public $isEditing = false;
    public $imagenes = [], $cbct = [];
    public $selectedTratamiento, $status = "Set Up";

    protected $rules = [
        'num_paciente' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'fecha_nacimiento' => 'required|date',
        'email' => 'required|email|max:255',
        'telefono' => 'required|string|max:20',
        'revision' => 'nullable|date',
        'observaciones' => 'nullable|string|max:255',
        'obser_cbct' => 'nullable|string',
        'selectedTratamiento' => 'required|exists:tratamientos,id',
        'imagenes.*' => 'image',
        'cbct.*' => 'file|mimes:zip',
    ];

    public function mount()
    {
        $this->tratamientos = Tratamiento::all();
        $this->clinica_id = Auth::user()->clinicas()->first()->id;
    }

    public function render()
    {
        // Paginar los pacientes con el método 'paginate'
        $pacientes = Paciente::with('tratEtapas')
            ->where('clinica_id', $this->clinica_id) // Si necesitas filtrar por clínica
            ->paginate(5); // Cambia 10 por el número de ítems por página que desees

        return view('livewire.pacientes', [
            'pacientes' => $pacientes,
        ]);
    }

    public function edit(Paciente $paciente)
    {
        $this->paciente_id = $paciente->id;
        $this->num_paciente = $paciente->num_paciente;
        $this->name = $paciente->name;
        $this->email = $paciente->email;
        $this->telefono = $paciente->telefono;
        $this->fecha_nacimiento = $paciente->fecha_nacimiento;
        $this->selectedTratamiento = $paciente->tratEtapas->isNotEmpty() ? $paciente->tratEtapas->first()->id : null;
        $this->revision = $paciente->revision;
        $this->telefono = $paciente->telefono;
        $this->observaciones = $paciente->observaciones;
        $this->obser_cbct = $paciente->obser_cbct;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function showCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function save()
    {
            $this->validate();

            if (is_null($this->clinica_id)) {
                $this->dispatch('pacienteError');
                return;
            }
            if ($this->paciente_id) {
                $paciente = Paciente::find($this->paciente_id);
                $paciente->update([
                    'num_paciente' => $this->num_paciente,
                    'name' => $this->name,
                    'email' => $this->email,
                    'fecha_nacimiento' => $this->fecha_nacimiento,
                    'telefono' => $this->telefono,
                    'revision' => $this->revision,
                    'observaciones' => $this->observaciones,
                    'obser_cbct' => $this->obser_cbct,
                ]);
                $this->dispatch('pacienteEdit');
            } else {
                $pacienteNew = Paciente::create([
                    'num_paciente' => $this->num_paciente,
                    'name' => $this->name,
                    'fecha_nacimiento' => $this->fecha_nacimiento,
                    'email' => $this->email,
                    'telefono' => $this->telefono,
                    'revision' => $this->revision,
                    'observaciones' => $this->observaciones,
                    'obser_cbct' => $this->obser_cbct,
                    'clinica_id' => $this->clinica_id,
                ]);

                $tratamiento = $pacienteNew->tratEtapas()->attach($this->selectedTratamiento, ['status' => $this->status]);
                if ($tratamiento) {
                    if (!empty($this->imagenes)) {
                        foreach ($this->imagenes as $key => $imagen) {
                            $path = $imagen->store('imagenPaciente', 'public');
                            Imagen::create([
                                'trat_etapa_id' => $tratamiento->id,
                                'ruta' => $path,
                            ]);
                        }
                    }

                    // Guardar archivos CBCT
                    if (!empty($this->cbct)) {
                        foreach ($this->cbct as $archivo) {
                            $path = $archivo->store('pacienteCbct', 'public');
                            Archivo::create([
                                'trat_etapa_id' => $tratamiento->pivote->id,
                                'ruta' => $path,
                            ]);
                        }
                    }
                }
                $pacienteNew->save();
                $this->dispatch('nuevoPaciente');
            }

        $this->resetForm();
        $this->showModal = false;
        $this->resetPage();

    }

    public function saveFiles($paciente)
    {
        $tratamiento = $paciente->tratEtapas()->first();
        if ($tratamiento) {
            if (!empty($this->imagenes)) {
                foreach ($this->imagenes as $key => $imagen) {
                    $path = $imagen->store('imagenPaciente', 'public');
                    Imagen::create([
                        'trat_etapa_id' => $tratamiento->pivot->id,
                        'ruta' => $path,
                    ]);
                }
            }

            // Guardar archivos CBCT
            if (!empty($this->cbct)) {
                foreach ($this->cbct as $archivo) {
                    $path = $archivo->store('pacienteCbct', 'public');
                    Archivo::create([
                        'trat_etapa_id' => $tratamiento->pivote->id,
                        'ruta' => $path,
                    ]);
                }
            }
        }
    }

    public function delete($id)
    {
        Paciente::find($id)->delete();
        $this->session()->flash('message', ['style' => 'success', 'message' => 'Paciente eliminado con éxito.']);
        $pacientes = Paciente::with('tratEtapas.tratamiento')->get();
    }

    public function showPaciente($id_paciente){
        return redirect()->route('pacientes-show',$id_paciente);
    }

    public function resetForm()
    {
        $this->reset(['name',
                'email',
                'telefono',
                'num_paciente',
                'fecha_nacimiento',
                'selectedTratamiento',
                'revision', 'observaciones',
                'obser_cbct', 'odontograma_obser',
                'imagenes', 'cbct', 'isEditing'
            ]);
    }

    public function close()
    {
        $this->showModal = false;
    }
}

// public function updateStatus($pacienteId, $tratamientoId, $status)
//     {
//         $paciente = Paciente::findOrFail($pacienteId);
//         $paciente->tratEtapas()->updateExistingPivot($tratamientoId, ['status' => $status]);

//         $this->loadPacientes();

//         session()->flash('flash.bannerStyle', 'success');
//         session()->flash('flash.banner', 'Estado del tratamiento actualizado con éxito.');
//     }
