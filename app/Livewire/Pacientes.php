<?php

namespace App\Livewire;

use App\Models\Archivo;
use App\Models\Etapa;
use App\Models\Imagen;
use App\Models\Paciente;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;
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
    public $imagenes = [], $cbcts = [];
    public $selectedTratamiento, $status = "Set Up";

    public $search = '';
    public $ordenar = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'ordenar' => ['except' => ''],
    ];

    public $statuses = [
        'En proceso' => 'bg-green-600',
        'Pausado' => 'bg-blue-600',
        'Finalizado' => 'bg-red-600',
        'Set Up' => 'bg-yellow-600'
    ];

    protected $rules = [
        'num_paciente' => 'required|string|max:50',
        'name' => 'required|string|max:255',
        'fecha_nacimiento' => 'required|date',
        'email' => 'required|email|max:255',
        'telefono' => 'required|string|max:20',
        'revision' => 'nullable|date',
        'observacion' => 'nullable|string|max:255',
        'obser_cbct' => 'nullable|string',
        'selectedTratamiento' => 'required|exists:tratamientos,id',
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
        $this->clinica_id = Auth::user()->clinicas->first()->id;
    }

    public function render()
{
    // Subconsulta para obtener el tratamiento más reciente de cada paciente
    $subQuery = DB::table('paciente_trat')
        ->select('paciente_id', DB::raw('MAX(updated_at) as trat_reciente'))
        ->groupBy('paciente_id');

    // Consulta principal para obtener los pacientes con su tratamiento más reciente
    $pacientesQuery = Paciente::select(
            'pacientes.*',
            'tratamientos.name as tratamiento_nombre',
            'paciente_trat.updated_at as tratamiento_fecha',
            'etapas.status as etapa_status',
            'etapas.name as etapa_name',
            'etapas.id as etapa_id'
        )
        ->joinSub($subQuery, 'last_treatments', function ($join) {
            $join->on('pacientes.id', '=', 'last_treatments.paciente_id');
        })
        ->join('paciente_trat', function ($join) {
            $join->on('pacientes.id', '=', 'paciente_trat.paciente_id')
                ->on('paciente_trat.updated_at', '=', 'last_treatments.trat_reciente');
        })
        ->join('tratamientos', 'paciente_trat.trat_id', '=', 'tratamientos.id')
        ->leftJoin('etapas', function ($join) {
            $join->on('paciente_trat.trat_id', '=', 'etapas.trat_id')
                 ->whereIn('etapas.id', function ($query) {
                     $query->select(DB::raw('MAX(id)'))
                         ->from('etapas')
                         ->whereColumn('etapas.trat_id', 'paciente_trat.trat_id')
                         ->groupBy('etapas.trat_id');
                 });
        })
        ->when($this->search, function($query) {
            $query->where(function($subQuery) {
                $subQuery->where('pacientes.name', 'like', '%' . $this->search . '%')
                        ->orWhere('pacientes.num_paciente', 'like', '%' . $this->search . '%');
            });
        });

    // Ordenar por el criterio seleccionado
    $pacientesQuery->orderBy(
        $this->ordenar == 'name' ? 'pacientes.name' :
        ($this->ordenar == 'recientes' ? 'paciente_trat.updated_at' : 'pacientes.id'),
        $this->ordenar == 'recientes' ? 'desc' : 'asc'
    );

    $pacientes = $pacientesQuery->paginate(15);

    return view('livewire.pacientes', [
        'pacientes' => $pacientes,
    ]);
}


    public function showCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();
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

        // Asociar tratamiento al paciente
        PacienteTrat::create([
            'paciente_id' => $pacienteNew->id,
            'trat_id' => $this->selectedTratamiento,
        ]);

        // Crear etapa inicial
        $etapaInicial = Etapa::create([
            'trat_id' => $this->selectedTratamiento,
            'name' => 'Inicio',
            'status' => 'Set Up',
        ]);

        // Guardar imágenes
        if ($this->imagenes) {
            foreach ($this->imagenes as $imagen) {
                $path = $imagen->store('imagenPaciente', 'public');
                Imagen::create([
                    'etapa_id' => $etapaInicial->id,
                    'ruta' => $path,
                ]);
            }
        }

        // Guardar archivos CBCT
        if ($this->cbcts) {
            foreach ($this->cbcts as $cbct) {
                $path = $cbct->store('pacienteCbct', 'public');
                Archivo::create([
                    'etapa_id' => $etapaInicial->id,
                    'ruta' => $path,
                ]);
            }
        }

        $this->dispatch('nuevoPaciente');
        $this->resetForm();
        $this->showModal = false;
        $this->resetPage();
    }

    public function estado($tratId, $newStatus)
    {
        // Encontrar la etapa correspondiente y actualizar el estado
        $etapa = Etapa::where('trat_id', $tratId)->first();

        if ($etapa) {
            $etapa->status = $newStatus;
            $etapa->save();

            // Actualizar el estado localmente (en caso de ser necesario)
            $etapa->status = $newStatus;
            $this->mostrar = false; // Cerrar el menú
        }
        $this->dispatch('estadoActualizado');
        $this->resetPage();
    }

    public function showPaciente($id_paciente){
        return redirect()->route('pacientes-show',$id_paciente);
    }
    public function showHistorial($id)
    {
        return redirect()->route('paciente-historial', ['id' => $id]);
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
                'imagenes', 'cbcts', 'isEditing'
            ]);
    }

    public function delete($id)
    {
        Paciente::find($id)->delete();
        $this->dispatch('deletePaciente');
        // $pacientes = Paciente::with('tratEtapas.tratamiento')->get();
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
