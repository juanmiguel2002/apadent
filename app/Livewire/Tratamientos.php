<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tratamiento;
use App\Mail\NewTratamiento;
use App\Models\Etapa;
use App\Models\Fase;
use App\Models\TratamientoEtapa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class Tratamientos extends Component
{
    public $tratamientos, $trat_id;
    public $clinica;
    public $showModal = false, $isEditing = false;
    public $name, $descripcion;

    public function mount(){

        // Filtrar los tratamientos por la clinica del usuario logueado (solo salen los que los usuarios de la clinica estan asignados)
        $user = Auth::user();
        $this->tratamientos = Tratamiento::whereHas('pacientes.paciente.clinicas', function ($query) use ($user) {
            $query->whereIn('id', $user->clinicas->pluck('id'));
        })->get();
        //sacar el nombre de la clinica
        $this->clinica = $user->clinicas;
        // dd($this->tratamientos);
    }

    public function render()
    {
        return view('livewire.tratamientos');
    }

    public function showCreateModal($tratamientoId = null)
    {
        $this->reset([
            'name', 'descripcion'
        ]);
        $this->trat_id = $tratamientoId;
        if ($this->trat_id) {
            $this->isEditing = true;
        }else{
            $this->isEditing = false;
        }
        $this->showModal = true;
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
            $etapaInicio = Etapa::create([
                'name' => 'Inicio',
                'fecha_ini' => now(),
                'status' => 'Set Up',
            ]);

            Fase::create([
                'trat_id' => $tratamiento->id,
                'etapa_id' => $etapaInicio->id,
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
    }
}
