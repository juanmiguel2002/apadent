<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tratamiento;
use App\Mail\NewTratamiento;
use App\Models\Clinica;
use App\Models\Fase;
use App\Models\Paciente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class Tratamientos extends Component
{
    public $tratamientos, $trat_id;
    public $clinica, $clinicas;

    public $clinicaSelected;
    public $showModal = false, $isEditing = false;
    public $name, $descripcion;

    public function mount(){

        // Filtrar los tratamientos por la clinica del usuario logueado (solo salen los que los usuarios de la clinica estan asignados)
        $user = Auth::user();
        // $this->tratamientos = Tratamiento::whereHas('pacientes.paciente.clinicas', function ($query) use ($user) {
        //     $query->whereIn('id', $user->clinicas->pluck('id'));
        // })->get();

        // saca todos los tratamientos
        $this->tratamientos = Tratamiento::all();

        //sacar el nombre de la clinica
        $this->clinica = $user->clinicas;
        $this->clinicas = Clinica::all();
        // dd($this->tratamientos);

    }

    public function pacientesTrat($trat) {

        if(auth()->user()->hasRole('admin')){
            return Paciente::whereHas('tratamientos', function($query) use ($trat) {
                $query->where('trat_id', $trat);
            })->count();
        }else {
            return Paciente::whereHas('clinicas', function($query) {
                $query->where('id', $this->clinica[0]->id);
            })
            ->whereHas('tratamientos', function($query) use ($trat) {
                $query->where('trat_id', $trat);
            })
            ->count();
        }

        // Obtener el número de pacientes que están en la clinica seleccionada y tienen el tratamiento seleccionado

    }

    public function render()
    {
        return view('livewire.tratamientos');
    }

    public function showCreateModal($tratamientoId = null)
    {
        $this->trat_id = $tratamientoId;
        $this->isEditing = $this->trat_id !== null;
        $this->showModal = true;

        if ($this->isEditing) {
            $tratamiento = Tratamiento::find($this->trat_id);
            if ($tratamiento) {
                $this->name = $tratamiento->name;
                $this->descripcion = $tratamiento->descripcion;
            } else {
                $this->dispatch('error', 'Tratamiento no encontrado.');
                $this->close();
            }
        } else {
            $this->reset(['name', 'descripcion']);
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'string|required|max:50',
            'descripcion' => 'string|required|max:255'
        ]);
        // Obtener la clinica del usuario logueado
        $clinica = Auth::user()->clinicas->first();

        $tratamiento = Tratamiento::updateOrCreate(['id' => $this->trat_id],[
            'name' => $this->name,
            'descripcion' => $this->descripcion,
        ]);

        if(!$this->trat_id){
            Fase::create([
                'name' => 'Fase 1',
                'trat_id' => $tratamiento->id,
            ]);
        }


        $this->dispatch('tratamiento', $this->trat_id ? 'Tratamiento Actualizado.' : 'Tratamiento Creado');

        // Mail::to($clinica->email)->send(new NewTratamiento($tratamiento, $this->trat_id ? 'Tratamiento actualizado.' : 'Tratamiento Creado',$this->trat_id ? true : false));

        $this->close();
        $this->mount();
    }

    public function close()
    {
        $this->showModal = false;
        $this->reset([
            'name', 'descripcion'
        ]);
    }
}
