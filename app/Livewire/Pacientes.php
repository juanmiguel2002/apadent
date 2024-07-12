<?php

namespace App\Livewire;

use App\Models\Paciente;
use App\Models\Tratamiento;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Pacientes extends Component
{
    public $pacientes, $tratamientos, $clinica_id;
    public $name, $email, $telefono, $num_paciente, $fecha_nacimiento, $revision, $observaciones, $obser_cbct, $odontograma_obser;
    public $showModal = false;
    public $isEditing = false;
    public $paciente_id;
    public $selectedTratamiento, $status = "Set Up";

    protected $rules = [
        'num_paciente' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'fecha_nacimiento' => 'required|date',
        'selectedTratamiento' => 'required|exists:tratamientos,id',
        'revision' => 'nullable|date',
        'telefono' => 'required|string|max:20',
        'observaciones' => 'nullable|string|max:255',
        'obser_cbct' => 'nullable|string',
    ];

    public function mount()
    {
        $this->loadPacientes();
        $this->tratamientos = Tratamiento::all();
        $this->clinica_id = Auth::user()->clinicas()->first()->id;
    }

    public function loadPacientes()
    {
        $this->pacientes = Paciente::with('tratEtapas')->get();
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
        $this->odontograma_obser = $paciente->odontograma_obser;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function create()
    {
        $this->resetFields();
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
        // dd($this->selectedTratamiento);

        if (is_null($this->clinica_id)) {
            // session()->flash('message', ['style' => 'danger', 'message' => 'Error: Clínica no asignada.']);
            $this->dispatch('pacienteEdit');
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
                'telefono' => $this->telefono,
                'observaciones' => $this->observaciones,
                'obser_cbct' => $this->obser_cbct,
            ]);
            $paciente->tratEtapas()->sync([$this->selectedTratamiento]); // Utiliza sync() para actualizar la relación muchos a muchos
            $this->dispatch('pacienteEdit');

        } else {
            $pacienteNew = Paciente::create([
                'num_paciente' => $this->num_paciente,
                'name' => $this->name,
                'email' => $this->email,
                'telefono' => $this->telefono,
                'fecha_nacimiento' => $this->fecha_nacimiento,
                'revision' => $this->revision,
                'observaciones' => $this->observaciones,
                'obser_cbct' => $this->obser_cbct,
                'clinica_id' => $this->clinica_id, // clinica id se pone automaticamente
            ]);
            // Aquí se podría asignar el tratamiento si es necesario
            // $pacienteNew->tratEtapas()->sync([$this->selectedTratamiento]);
            $pacienteNew->tratEtapas()->attach($this->selectedTratamiento, ['status' => $this->status]);;

            $this->dispatch('nuevoPaciente');
        }

        $this->resetForm();
        $this->loadPacientes();
        $this->showModal = false;
    }

    public function delete($id)
    {
        Paciente::find($id)->delete();
        $this->session()->flash('message', ['style' => 'success', 'message' => 'Paciente eliminado con éxito.']);
        $this->pacientes = Paciente::with('tratEtapas.tratamiento')->get();
    }

    public function showPaciente(Paciente $paciente){

    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->telefono = '';
        $this->num_paciente = '';
        $this->fecha_nacimiento = '';
        $this->selectedTratamiento = '';
        $this->revision = '';
        $this->observaciones = '';
        $this->obser_cbct = '';
        $this->odontograma_obser = '';
        $this->isEditing = false;
        $this->paciente_id = '';
    }

    public function render()
    {
        return view('livewire.pacientes');
    }

    public function close()
    {
        $this->showModal = false;
    }
}
