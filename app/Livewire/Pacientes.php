<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Mail\CambioEstado;
use App\Mail\NotificacionNuevoPaciente;
use App\Models\Archivo;
use App\Models\Clinica;
use App\Models\Etapa;
use App\Models\Fase;
use App\Models\Paciente;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;
use App\Models\TratamientoEtapa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class Pacientes extends Component
{
    use WithFileUploads, WithPagination;

    public $tratamientos, $clinica_id, $paciente, $paciente_id;
    public $num_paciente, $name, $apellidos, $email, $telefono, $fecha_nacimiento;
    public $observacion, $obser_cbct;
    public $showModal = false, $mostrar = false;
    public $imagenes = [], $cbct = [], $img_paciente;
    public $selectedTratamiento, $status = "Set Up", $activo = false;

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
        'num_paciente' => 'required|integer|unique:pacientes,num_paciente',
        'name' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'fecha_nacimiento' => 'required|date',
        'email' => 'required|email|max:255',
        'telefono' => 'required|string|max:20',
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

    public function toggleVerInactivos()
    {
        $this->activo = !$this->activo;
    }

    public function mount()
    {
        $this->tratamientos = Tratamiento::all();
    }

    public function render()
    {
        // Definir la columna y dirección de ordenación predeterminadas
        $orderByColumn = 'id';
        $orderByDirection = 'asc';

        // Determinar la columna de ordenación basada en la selección del usuario
        switch ($this->ordenar) {
            case 'recientes':
                $orderByColumn = 'created_at';
                $orderByDirection = 'desc';
                break;
            case 'name':
                $orderByColumn = 'name';
                $orderByDirection = 'asc';
                break;
            default:
                break;
        }

        $pacientes = Paciente::select(
            'pacientes.*',
            'tratamientos.id as tratamiento_id',
            'tratamientos.name as tratamiento_name',
            'tratamientos.descripcion as tratamiento_descripcion',
            'etapas.id as etapa_id',
            'etapas.name as etapa_name',
            'etapas.status as etapa_status'
        )
        ->where(function ($query) {  // Búsqueda
            $query->where('pacientes.name', 'like', '%' . $this->search . '%')
                ->orWhere('pacientes.apellidos', 'like', '%' . $this->search . '%')
                ->orWhere('pacientes.telefono', 'like', '%' . $this->search . '%');
        })
        ->join('clinicas', 'pacientes.clinica_id', '=', 'clinicas.id') // Relación entre clínicas y pacientes
        ->leftJoin('paciente_trat', function ($join) {
            $join->on('pacientes.id', '=', 'paciente_trat.paciente_id')
                ->whereRaw('paciente_trat.id = (SELECT MAX(id) FROM paciente_trat WHERE paciente_id = pacientes.id)');
        })
        ->leftJoin('tratamientos', 'paciente_trat.trat_id', '=', 'tratamientos.id')
        ->leftJoin('fases', 'tratamientos.id', '=', 'fases.trat_id')
        ->leftJoin('etapas', 'fases.id', '=', 'etapas.fases_id')
        ->leftJoin('archivos', 'etapas.id', '=', 'archivos.etapa_id') // Archivos relacionados con etapas
        ->where('pacientes.activo', $this->activo ? 0 : 1)  // Filtrar solo pacientes activos o inactivos
        ->where(function ($query) {
            $query->where(function ($subquery) {
                // Buscar etapas que no estén finalizadas
                $subquery->where('etapas.status', '!=', 'Finalizado')
                        ->orWhereNull('etapas.status');
            })
            ->orWhere(function ($subquery) {
                // Mostrar la última etapa finalizada si no hay etapas activas
                $subquery->where('etapas.status', '=', 'Finalizado')
                        ->whereNotExists(function ($subSubquery) {
                            // Verificar si hay etapas no finalizadas
                            $subSubquery->select(DB::raw(1))
                                        ->from('etapas as e')
                                        ->whereColumn('e.fases_id', 'fases.id')
                                        ->where('e.status', '!=', 'Finalizado');
                        });
            });
        })
        ->groupBy('pacientes.id')
        ->orderBy($orderByColumn, $orderByDirection)
        ->paginate($this->perPage);

        return view('livewire.pacientes', [
            'pacientes' => $pacientes,
        ]);
    }


    // CREAR PACIENTE
    public function showCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

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
        ]);

        // Obtener la clínica del usuario autenticado
        $clinica = Auth::user()->clinicas()->first();

        // Asociar el paciente con la clínica
        $clinica->pacientes()->attach($paciente->id);

        // 2. Asociar tratamiento al paciente
        PacienteTrat::create([
            'paciente_id' => $paciente->id,
            'trat_id' => $this->selectedTratamiento,
        ]);

        // // 3. Obtener las etapas del tratamiento seleccionado y asociarlas al paciente
        $etapas = Fase::where('trat_id', $this->selectedTratamiento)->get();

        // 4. Crear carpetas para el paciente (si es necesario)
        $this->createPacienteFolders($paciente->id);

        // Generamos los nombres de la clínica i el paciente
        $clinicaName = preg_replace('/\s+/', '_', trim(Auth::user()->clinicas->first()->name));
        $pacienteName = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $paciente->apellidos));
        $pacienteFolder = $clinicaName . '/pacientes/' . $pacienteName;

        // 5. Subir la foto del paciente (si existe)
        $paciente->refresh();
        if ($this->img_paciente) {
            $extension = $this->img_paciente->getClientOriginalExtension();
            $fileName = $pacienteName . '.' . $extension;
            $path = $this->img_paciente->storeAs($pacienteFolder . '/fotoPaciente', $fileName ,'public');

            $paciente->url_img = $path;
            $paciente->save();
        }

        // Subir múltiples imágenes del paciente, si existen
        if ($this->imagenes && is_array($this->imagenes)) {
            foreach ($this->imagenes as $key => $imagen) {
                foreach ($etapas as $etapa) {
                    $extension = $imagen->getClientOriginalExtension();
                    $fileName = "EtapaInicio". $key . '.' . $extension;
                    $path = $imagen->storeAs($pacienteFolder . '/imgEtapa', $fileName, 'clinicas');

                    // Guardar la ruta de la imagen en la tabla de archivos
                    $archivo = Archivo::create([
                        'ruta' => $path,
                        'tipo' => $extension,
                        'etapa_id' => $etapa->etapa_id,
                    ]);
                }
            }
            $archivo->save();
        }

        // Subir múltiples CBCT, si existen
        if ($this->cbct && is_array($this->cbct)) {
            foreach ($this->cbct as $cbctFile) {
                $path = $cbctFile->store($pacienteFolder . '/CBCT', 'clinicas');
                $extension = $cbctFile->getClientOriginalExtension();

                // Guardar la ruta del CBCT en la tabla de archivos
                $archivo= Archivo::create([
                    'ruta' => $path,
                    'tipo' => $extension,
                    'etapa_id' => $etapas->etapa_id,
                ]);
            }
            $archivo->save();
        }

        // Enviar email a la clínica
        if ($clinica && $clinica->email) {
            Mail::to($clinica->email)->send(new NotificacionNuevoPaciente($paciente));
        }

        //Disparar evento y resetear formulario
        $this->dispatch('nuevoPaciente');
        $this->resetForm();
        $this->showModal = false;
        $this->resetPage();
    }

    public function createPacienteFolders($pacienteId)
    {
        // Buscar al paciente por ID
        $paciente = Paciente::findOrFail($pacienteId);

        // Obtener la primera clínica asociada al usuario autenticado
        $clinica = Auth::user()->clinicas->first();

        if (!$clinica) {
            throw new \Exception("No se encontró ninguna clínica asociada al usuario.");
        }

        // Normalizar el nombre de la clínica y del paciente para evitar problemas con los nombres de directorios
        $nombreClinica = preg_replace('/\s+/', '_', trim($clinica->name));
        $nombrePaciente = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $paciente->apellidos));

        // Ruta base para la carpeta de la clínica y del paciente
        // $clinicaFolder = 'clinicas/' . $nombreClinica;
        $pacienteFolder = $nombreClinica . '/pacientes/' . $nombrePaciente;

        // Lista de subcarpetas que deseas crear dentro de la carpeta del paciente
        $subFolders = ['imgEtapa', 'CBCT', 'imgPaciente', 'archivoEtapa', 'Stripping'];

        // Crear la carpeta de pacientes si no existe
        if (!Storage::disk('clinicas')->exists($pacienteFolder)) {
            Storage::disk('clinicas')->makeDirectory($pacienteFolder);
        }

        // Crear las subcarpetas dentro de la carpeta del paciente
        foreach ($subFolders as $subFolder) {
            $subFolderPath = $pacienteFolder . '/' . $subFolder;
            if (!Storage::disk('clinicas')->exists($subFolderPath)) {
                Storage::disk('clinicas')->makeDirectory($subFolderPath);
            }
        }
    }

    // CAMBIAR ESTADO PACIENTE ETAPA
    public function estado($pacienteId, $etapaId, $newStatus)
    {
        $tratamientoEtapa = TratamientoEtapa::where('etapa_id', $etapaId)->first();

        if ($tratamientoEtapa) {
            if($newStatus === 'Finalizado'){
                Etapa::where('id', $tratamientoEtapa->etapa_id)
                ->update(['status' => $newStatus, 'fecha_fin' => now()]);
            }else{
                Etapa::where('id', $tratamientoEtapa->etapa_id)
                ->update(['status' => $newStatus]);
            }

            $this->mostrar = false; // Cerrar el menú
            $this->dispatch('estadoActualizado');
            $this->resetPage();

            // Enviar email a la clínica
            $paciente = Paciente::find($pacienteId);
            $clinica = Clinica::find($paciente->clinica_id); // Obtener la clínica asociada al paciente
            $etapa = Etapa::find($etapaId);
            $trat = Tratamiento::find($tratamientoEtapa->trat_id);

            if ($clinica && $clinica->email) {
                Mail::to($clinica->email)->send(new CambioEstado($paciente, $newStatus, $etapa, $trat));
            }

        }
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
                'observacion','obser_cbct',
                'imagenes', 'cbct', 'img_paciente'
            ]);
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
