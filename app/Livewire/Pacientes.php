<?php

namespace App\Livewire;

use App\Models\Archivo;
use App\Models\Etapa;
use App\Models\Paciente;
use App\Models\PacienteEtapas;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;
use App\Models\TratamientoEtapa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Pacientes extends Component
{
    use WithFileUploads, WithPagination;

    public $tratamientos, $clinica_id, $paciente;
    public $name, $apellidos, $email, $telefono, $num_paciente, $fecha_nacimiento, $revision, $observacion, $obser_cbct, $odontograma_obser;
    public $showModal = false, $isEditing = false, $mostrar = false;
    public $imagenes = [], $cbcts = [], $img_paciente;
    public $selectedTratamiento, $status = "Set Up";

    public $search = '';
    public $ordenar = '';
    public $perPage = 25; //Para filtrar cuando se ve

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
        'num_paciente' => 'required|integer',
        'name' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'fecha_nacimiento' => 'required|date',
        'email' => 'required|email|max:255',
        'telefono' => 'required|string|max:20',
        'observacion' => 'nullable|string|max:255',
        'obser_cbct' => 'nullable|string',
        'selectedTratamiento' => 'required|exists:tratamientos,id',
        'img_paciente' =>'nullable|image'
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

        // Definir la columna y dirección de ordenación predeterminadas
        $orderByColumn = 'id';
        $orderByDirection = 'asc';

        // Determinar la columna de ordenación basada en la selección del usuario
        switch ($this->ordenar) {
            case 'recientes':
                $orderByColumn = 'created_at'; // Suponiendo que estás utilizando 'created_at' para ordenar por los más recientes
                $orderByDirection = 'desc';
                break;
            case 'name':
                $orderByColumn = 'name';
                $orderByDirection = 'asc';
                break;
            default:
                $orderByColumn;
                $orderByDirection;
                break;
        }

        $pacientes = Paciente::select(
            'pacientes.*',
            'tratamientos.id as tratamiento_id',
            'tratamientos.name as tratamiento_name',
            'etapas.id as etapa_id',
            'etapas.name as etapa_name',
            'paciente_etapas.status as etapa_status'
        )
        ->join('clinicas', 'pacientes.clinica_id', '=', 'clinicas.id')
        ->leftJoin('paciente_trat', 'pacientes.id', '=', 'paciente_trat.paciente_id')
        ->leftJoin('tratamientos', 'paciente_trat.trat_id', '=', 'tratamientos.id')
        ->leftJoin('tratamiento_etapa', 'tratamientos.id', '=', 'tratamiento_etapa.trat_id')
        ->leftJoin('etapas', 'tratamiento_etapa.etapa_id', '=', 'etapas.id')
        ->leftJoin('paciente_etapas', function ($join) {
            $join->on('pacientes.id', '=', 'paciente_etapas.paciente_id')
                 ->on('etapas.id', '=', 'paciente_etapas.etapas_id');
        })
        ->where(function ($query) {
            $query->where('pacientes.name', 'like', '%' . $this->search . '%')
                  ->orWhere('pacientes.telefono', 'like', '%' . $this->search . '%');
        })
        ->whereIn('etapas.id', function ($query) {
            $query->select(DB::raw('MIN(etapas.id)'))
                ->from('etapas')
                ->join('tratamiento_etapa', 'etapas.id', '=', 'tratamiento_etapa.etapa_id')
                ->join('paciente_trat', 'tratamiento_etapa.trat_id', '=', 'paciente_trat.trat_id')
                ->whereColumn('paciente_trat.paciente_id', 'pacientes.id')
                ->groupBy('paciente_trat.trat_id');
        })
        ->orderBy($orderByColumn, $orderByDirection)
        ->paginate($this->perPage);

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
        // Iniciar una transacción para asegurar que todas las operaciones se completen correctamente
        // 1. Crear el paciente
        $paciente = Paciente::create([
            'num_paciente' => $this->num_paciente,
            'name' => $this->name,
            'apellidos' => $this->apellidos,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'observacion' => $this->observacion,
            'obser_cbct' => $this->obser_cbct,
            'clinica_id' => $this->clinica_id,
        ]);

        // 2. Asociar tratamiento al paciente
        PacienteTrat::create([
            'paciente_id' => $paciente->id,
            'trat_id' => $this->selectedTratamiento,
        ]);

        // 3. Obtener las etapas del tratamiento seleccionado y asociarlas al paciente
        $etapas = TratamientoEtapa::where('trat_id', $this->selectedTratamiento)->get();

        foreach ($etapas as $etapa) {
            $pacienteEtapa = PacienteEtapas::create([
                'paciente_id' => $paciente->id,
                'etapas_id' => $etapa->etapa_id,
                'fecha_ini' => now(),
                'fecha_fin' => null,
                'status' => 'Set Up',
            ]);
        }
        $pacienteEtapa->save();

        // 4. Crear carpetas para el paciente (si es necesario)
        // $this->createPacienteFolders($paciente->id);

        // 5. Disparar evento y resetear formulario
        $this->dispatch('nuevoPaciente');
        $this->resetForm();
        $this->showModal = false;
        $this->resetPage();

    }

    public function createPacienteFolders($pacienteId)
    {
        // Buscar al paciente por ID
        $paciente = Paciente::findOrFail($pacienteId);

        // Obtener la clínica del paciente
        $clinica = Auth::user()->clinicas->first()->id;

        // Normalizar el nombre de la clínica y el paciente para evitar problemas con los nombres de directorios
        $nombreClinica = preg_replace('/\s+/', '_', trim($clinica));
        $nombrePaciente = preg_replace('/\s+/', '_', trim($paciente->name));

        // Ruta base para la carpeta de la clínica y del paciente
        $clinicaFolder = 'clinicas/' . $nombreClinica;
        $pacienteFolder = $clinicaFolder . '/pacientes/' . $nombrePaciente;

        // Lista de subcarpetas que deseas crear dentro de la carpeta del paciente
        $subFolders = ['imgEtapa', 'CBCT', 'imgPaciente', 'fotoPaciente'];

        // Crear la carpeta de pacientes si no existe
        $pacientesFolder = $clinicaFolder . '/pacientes';
        if (!Storage::disk('public')->exists($pacientesFolder)) {
            if (!Storage::disk('public')->makeDirectory($pacientesFolder)) {
                throw new \Exception("No se pudo crear la carpeta de pacientes: {$pacientesFolder}");
            }
        }

        // Crear la carpeta principal del paciente dentro de la carpeta de pacientes
        if (!Storage::disk('public')->exists($pacienteFolder)) {
            if (!Storage::disk('public')->makeDirectory($pacienteFolder)) {
                throw new \Exception("No se pudo crear la carpeta del paciente: {$pacienteFolder}");
            }
        }

        // Crear las subcarpetas dentro de la carpeta del paciente
        foreach ($subFolders as $subFolder) {
            $subFolderPath = $pacienteFolder . '/' . $subFolder;
            if (!Storage::disk('public')->exists($subFolderPath)) {
                if (!Storage::disk('public')->makeDirectory($subFolderPath)) {
                    throw new \Exception("No se pudo crear la subcarpeta: {$subFolderPath}");
                }
            }
        }

        // return response()->json(['message' => 'Carpetas creadas exitosamente para el paciente ' . $paciente->name]);
    }

    public function estado($pacienteId, $tratId, $newStatus)
    {
        // Encontrar la etapa correspondiente para el paciente y tratamiento específico
        $etapa = Etapa::where('trat_id', $tratId)
                  ->whereHas('tratamiento', function($query) use ($pacienteId) {
                      $query->where('paciente_id', $pacienteId); // Verifica la relación del tratamiento con el paciente
                  })
                  ->first();

        if ($etapa) {
            // Actualizar el estado de la etapa
            $etapa->status = $newStatus;
            $etapa->save();

            // Actualizar el estado localmente si es necesario
            $this->mostrar = false; // Cerrar el menú
        }

        // Emitir un evento para notificar la actualización del estado
        $this->dispatch('estadoActualizado');
        // $this->resetPage(); // Opcional: Reiniciar la página si se usa paginación
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
                'apellidos',
                'email',
                'telefono',
                'num_paciente',
                'fecha_nacimiento',
                'selectedTratamiento',
                'observacion','obser_cbct', 'odontograma_obser',
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
