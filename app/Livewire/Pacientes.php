<?php

namespace App\Livewire;

use App\Models\Archivo;
use App\Models\Imagen;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Models\TratEtapa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Pacientes extends Component
{
    use WithFileUploads, WithPagination;

    public $tratamientos, $clinica_id, $paciente_id, $paciente;
    public $name, $email, $telefono, $num_paciente, $fecha_nacimiento, $revision, $observacion, $obser_cbct, $odontograma_obser;
    public $showModal = false, $isEditing = false, $mostrar = false;
    public $imagenes = [], $cbct = [];
    public $selectedTratamiento, $status = "Set Up";

    public $search = '';
    public $ordenar = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'ordenar' => ['except' => 'name'],
    ];

    public $statuses = [
        'En proceso' => 'bg-green-600',
        'Pausado' => 'bg-blue-600',
        'Finalizado' => 'bg-red-600',
        'Set Up' => 'bg-yellow-600'
    ];

    protected $rules = [
        'num_paciente' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'fecha_nacimiento' => 'required|date',
        'email' => 'required|email|max:255',
        'telefono' => 'required|string|max:20',
        'revision' => 'nullable|date',
        'observacion' => 'nullable|string|max:255',
        'obser_cbct' => 'nullable|string',
        'selectedTratamiento' => 'required|exists:tratamientos,id',
        'imagenes.*' => 'image',
        'cbct.*' => 'file|mimes:zip',
    ];
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingOrdenar()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->tratamientos = Tratamiento::all();
        $this->clinica_id = Auth::user()->clinicas()->first()->id;
    }

    // public function render()
    // {
    //     // Subconsulta para obtener el tratamiento más reciente de cada paciente
    //     $subQuery = DB::table('trat_etapas')
    //             ->select('paciente_id', DB::raw('MAX(updated_at) as last_treatment_date'))
    //             ->groupBy('paciente_id');

    //     // Consulta principal para obtener los pacientes con su tratamiento más reciente
    //     $pacientes = Paciente::select('pacientes.*', 'tratamientos.name as tratamiento_nombre', 'trat_etapas.updated_at as tratamiento_fecha', 'trat_etapas.status as tratamiento_status', 'trat_etapas.id as trat_id')
    //         ->joinSub($subQuery, 'last_treatments', function ($join) {
    //             $join->on('pacientes.id', '=', 'last_treatments.paciente_id');
    //         })
    //         ->join('trat_etapas', function ($join) {
    //             $join->on('pacientes.id', '=', 'trat_etapas.paciente_id')
    //                 ->on('trat_etapas.updated_at', '=', 'last_treatments.last_treatment_date');
    //         })
    //         ->join('tratamientos', 'trat_etapas.tratamiento_id', '=', 'tratamientos.id')
    //         ->orderBy('trat_etapas.updated_at', 'desc')
    //         ->paginate(5); // Cambia 5 por el número de ítems por página que desees

    //     return view('livewire.pacientes', [
    //         'pacientes' => $pacientes,
    //     ]);
    // }

    public function render()
    {
        // Subconsulta para obtener el tratamiento más reciente de cada paciente
        $subQuery = DB::table('trat_etapas')
                ->select('paciente_id', DB::raw('MAX(updated_at) as last_treatment_date'))
                ->groupBy('paciente_id');

        // Consulta principal para obtener los pacientes con su tratamiento más reciente
        $pacientes = Paciente::select('pacientes.*', 'tratamientos.name as tratamiento_nombre', 'trat_etapas.updated_at as tratamiento_fecha', 'trat_etapas.status as tratamiento_status', 'trat_etapas.id as trat_id')
            ->joinSub($subQuery, 'last_treatments', function ($join) {
                $join->on('pacientes.id', '=', 'last_treatments.paciente_id');
            })
            ->join('trat_etapas', function ($join) {
                $join->on('pacientes.id', '=', 'trat_etapas.paciente_id')
                    ->on('trat_etapas.updated_at', '=', 'last_treatments.last_treatment_date');
            })
            ->join('tratamientos', 'trat_etapas.tratamiento_id', '=', 'tratamientos.id')
            ->when($this->search, function($query) {
                $query->where(function($subQuery) {
                    $subQuery->where('pacientes.name', 'like', '%' . $this->search . '%')
                        ->orWhere('pacientes.num_paciente', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->ordenar, function($query) {
                if ($this->ordenar === 'name') {
                    $query->orderBy('pacientes.name');
                } elseif ($this->ordenar === 'recientes') {
                    $query->orderBy('trat_etapas.updated_at', 'desc');
                } else {
                    $query->orderBy('pacientes.id');
                }
            })
            ->paginate(5); // Cambia 5 por el número de ítems por página que desees

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
        $this->observacion = $paciente->observacion;
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
                    'observacion' => $this->observacion,
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
                    'observacion' => $this->observacion,
                    'obser_cbct' => $this->obser_cbct,
                    'clinica_id' => $this->clinica_id,
                ]);

                $pacienteNew->tratEtapas()->attach($this->selectedTratamiento, ['status' => $this->status]);
                $tratamiento = $pacienteNew->tratEtapas()->first();
                // dd($tratamiento);
                if ($tratamiento) {
                    if (!empty($this->imagenes)) {
                        foreach ($this->imagenes as $key => $imagen) {
                            $path = $imagen->store('imagenPaciente', 'public');
                            Imagen::create([
                                'trat_etapa_id' => $tratamiento->pivote->id,
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

    public function estado($tratId, $newStatus)
    {
        $tratEtapa = TratEtapa::find($tratId);
        $tratEtapa->status = $newStatus;
        $tratEtapa->save();

        $tratEtapa->status = $newStatus; // Actualizar el estado localmente
        $this->mostrar = false; // Cerrar el menú
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
                'revision', 'observacion',
                'obser_cbct', 'odontograma_obser',
                'imagenes', 'cbct', 'isEditing'
            ]);
    }

    public function delete($id)
    {
        Paciente::find($id)->delete();
        $this->session()->flash('message', ['style' => 'success', 'message' => 'Paciente eliminado con éxito.']);
        $pacientes = Paciente::with('tratEtapas.tratamiento')->get();
    }

    public function close()
    {
        $this->showModal = false;
    }
    public function toggleMenu()
    {
        $this->mostrar = !$this->mostrar;
    }

}
