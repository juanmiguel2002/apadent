<?php

namespace App\Livewire\Pacientes;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\CambioEstado;
use App\Models\Clinica;
use App\Models\Etapa;
use App\Models\Paciente;
use App\Models\Tratamiento;

class Pacientes extends Component
{
    use WithPagination;

    public $tratamientos;

    public $showModal = false;
    public $menuVisible = null;

    public $status = "Set Up", $activo = false;

    public $search = '';
    public $ordenar = '';
    public $perPage = 25; //Para filtrar cuando se ve
    public $clinicas, $clinicaSelected; //filtro de clinica
    public $hayClinicas = false; //para saber si hay clinicas

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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingOrdenar()
    {
        $this->resetPage();
    }

    public function toggleVerInactivos()
    {
        $this->activo = !$this->activo;
    }

    public function mount()
    {
        $this->tratamientos = Tratamiento::all();
        if(Auth::user()->hasRole('admin')) {
            $this->clinicas = Clinica::all();
        }
        $this->hayClinicas = Clinica::count() > 0;
    }

    public function render()
    {
        // Obtener el usuario autenticado
        $user = auth()->user();

        // Configuración de la ordenación
        $orderByColumn = $this->ordenar === 'recientes' ? 'pacientes.created_at' : ($this->ordenar === 'name' ? 'pacientes.name' : 'pacientes.num_paciente');
        $orderByDirection = $this->ordenar === 'recientes' ? 'desc' : 'asc';

        // Consulta base para pacientes
        $query = Paciente::with([
            'clinicas',
            'tratamientos' => function ($query) {
                $query->latest('paciente_trat.id'); // Último tratamiento asociado al paciente
            },
            'tratamientos.fases' => function ($query) {
                $query->latest('fases.id'); // Última fase asociada al tratamiento
            },
            'tratamientos.fases.etapas' => function ($query) {
                $query->latest('etapas.id'); // Última etapa asociada a la fase
            },
        ])
        ->whereHas('tratamientos.fases.etapas', function ($query) {
            $query->where('etapas.status', '<>', 'Finalizado') // Solo etapas no finalizadas
                ->orWhere('etapas.status', '=', 'Finalizado'); // También incluir las finalizadas
        })
        ->where(function ($query) { // Búsqueda
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                ->orWhere('telefono', 'like', '%' . $this->search . '%')
                ->orWhere('num_paciente', 'like', '%' . $this->search . '%');
        })
        ->where('activo', $this->activo ? 0 : 1); // Filtrar por pacientes activos o inactivos

        // Verificación de rol del usuario
        if ($user->hasRole('admin')) {
            // Si es admin, mostrar todos los pacientes (sin filtro de clínica)
            if ($this->clinicaSelected) {
                // Si se selecciona una clínica, filtrar solo pacientes de esa clínica
                $query->where('pacientes.clinica_id', $this->clinicaSelected);
            }
        } else {
            // Si es un usuario normal (no admin), solo mostrar los pacientes de la clínica asignada a ese usuario
            $query->where('pacientes.clinica_id', Auth::user()->clinicas->first()->id);
        }

        // Orden y paginación
        $pacientes = $query->orderBy($orderByColumn, $orderByDirection)
            ->paginate($this->perPage);

        return view('livewire.pacientes.index', [
            'pacientes' => $pacientes,
        ]);
    }

    // CAMBIAR ESTADO PACIENTE ETAPA
    public function estado($etapaId, $newStatus)
    {
        // Encuentra al paciente y su etapa actual
        $etapa = Etapa::find($etapaId);
        $paciente = Paciente::find($etapa->paciente_id);

        if (!$paciente) {
            session()->flash('error', 'Paciente no encontrado.');
            return;
        }

        if (!$etapa) {
            session()->flash('error', 'Etapa no encontrada para el paciente.');
            return;
        }

        // Actualizar el estado de la etapa
        if ($newStatus === 'Finalizado') {
            $etapa->update(['status' => $newStatus, 'fecha_fin' => now()]);
        } else {
            $etapa->status = $newStatus;
            $etapa->save();
        }

        // Cerrar el menú y notificar
        $this->menuVisible = null;
        $this->dispatch('estadoActualizado');
        $this->resetPage();

        // Opcional: Enviar email a la clínica
        $clinica = Clinica::find($paciente->clinica_id);
        if ($clinica && $clinica->email) {
            Mail::to($clinica->email)->send(new CambioEstado($paciente, $newStatus, $etapa, $this->tratamientos->first(),$clinica));
        }
    }

    public function toggleMenu($pacienteId)
    {
        $this->menuVisible = $this->menuVisible === $pacienteId ? null : $pacienteId;
    }
}
