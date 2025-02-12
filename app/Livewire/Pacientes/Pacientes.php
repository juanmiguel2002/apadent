<?php

namespace App\Livewire\Pacientes;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\CambioEstado;
use App\Mail\NotificacionNuevoPaciente;
use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Clinica;
use App\Models\Etapa;
use App\Models\Fase;
use App\Models\Paciente;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;

class Pacientes extends Component
{
    use WithFileUploads, WithPagination;

    public $tratamientos, $clinica_id, $paciente, $paciente_id;
    // añadir paciente
    public $num_paciente, $name, $apellidos, $email, $telefono, $fecha_nacimiento;
    public $observacion, $obser_cbct, $img_paciente;

    public $showModal = false;
    public $menuVisible = null;

    public $imagenes = [], $cbct = [], $rayos = [];
    public $selectedTratamiento, $status = "Set Up", $activo = false;

    public $search = '';
    public $ordenar = '';
    public $perPage = 25; //Para filtrar cuando se ve
    public $clinicas, $clinicaSelected;

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
        if(Auth::user()->hasRole('admin')) {
            $this->clinicas = Clinica::all();
        }
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
                ->orWhere('telefono', 'like', '%' . $this->search . '%');
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
            // Asumimos que el usuario tiene asociada una clínica y tiene un campo `clinica_id` o similar
            $query->where('pacientes.clinica_id', Auth::user()->clinicas->first()->id);
        }

        // Orden y paginación
        $pacientes = $query->orderBy($orderByColumn, $orderByDirection)
            ->paginate($this->perPage);

        return view('livewire.pacientes.index', [
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

        DB::transaction(function () {

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
                'clinica_id' => Auth::user()->clinicas->first()->id,
            ]);

            // 2. Asociar tratamiento al paciente
            PacienteTrat::create([
                'paciente_id' => $paciente->id,
                'trat_id' => $this->selectedTratamiento,
            ]);

            // 3. Generar etapas iniciales para todas las fases del tratamiento seleccionado
            $fases = Fase::where('trat_id', $this->selectedTratamiento)->get();

            foreach ($fases as $fase) {
                $etapa = Etapa::firstOrCreate(
                    [
                    'fase_id' => $fase->id,
                    'paciente_id' => $paciente->id,
                    ],
                    [
                    'name' => 'Inicio',
                    'fecha_ini' => now(),
                    'status' => 'Set Up',
                    ]
                );
            }

            // 4. Crear carpetas para el paciente (si es necesario)
            $this->createPacienteFolders($paciente->id);

            // Generar nombres para las carpetas
            $clinicaName = preg_replace('/\s+/', '_', trim(Auth::user()->clinicas->first()->name));
            $pacienteName = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $paciente->apellidos));
            $pacienteFolder = $clinicaName . '/pacientes/' . $pacienteName;

            // 5. Subir la foto del paciente (si existe)
            if ($this->img_paciente) {
                $extension = $this->img_paciente->getClientOriginalExtension();
                $fileName = $pacienteName . '.' . $extension;
                $path = $this->img_paciente->storeAs($pacienteFolder . '/fotoPaciente', $fileName, 'public');

                $paciente->url_img = $path;
                $paciente->save();
            }

            // 6. Subir múltiples imágenes del paciente (asociadas a la primera etapa)
            if ($this->imagenes && is_array($this->imagenes)) {
                foreach ($this->imagenes as $key => $imagen) {
                    $extension = $imagen->getClientOriginalExtension();
                    $fileName = "Imagen_" . $key . '.' . $extension;//nombre del archivo
                    $path = $imagen->storeAs($pacienteFolder . '/imgEtapa', $fileName, 'clinicas');

                    Archivo::create([
                        'ruta' => $path,
                        'tipo' => 'fotografias',
                        'extension' => $extension,
                        'etapa_id' => $etapa->id, // Asociar a la primera etapa creada
                    ]);
                }
            }

            // 7. Subir múltiples CBCT del paciente
            if ($this->cbct && is_array($this->cbct)) {
                foreach ($this->cbct as $cbctFile) {
                    $extension = $cbctFile->getClientOriginalExtension();
                    $fileName = "CBCT_". $paciente->name . '.' . $extension; // nombre del archivo
                    $path = $cbctFile->storeAs($pacienteFolder . '/CBCT', $fileName, 'clinicas');

                    Archivo::create([
                        'ruta' => $path,
                        'tipo' => 'CBCT',
                        'extension' => $extension,
                        'etapa_id' => $etapa->id,
                    ]);
                }
            }
            // 8. Subida de archivos rayos
            if ($this->rayos && is_array($this->rayos)) {
                foreach ($this->rayos as $key => $rayo) {
                    $extension = $rayo->getClientOriginalExtension();
                    $fileName = "Rayos_" . $key . '.' . $extension;//nombre del archivo
                    $path = $rayo->storeAs($pacienteFolder . '/Rayos', $fileName, 'clinicas');

                    Archivo::create([
                        'ruta' => $path,
                        'tipo' => 'rayos',
                        'extension' => $extension,
                        'etapa_id' => $etapa->id, // Asociar a la primera etapa creada
                    ]);
                }
            }

            // 9. Enviar email de notificación a la clínica
            $clinica = Clinica::find($paciente->clinica_id);
            $perfilPacienteUrl = route('pacientes-show', $paciente->id);

            if ($clinica && $clinica->email) {
                Mail::to($clinica->email)->send(new NotificacionNuevoPaciente($clinica, $paciente,null, null));
            }
            $admin = Clinica::where('email', 'juanmi0802@gmail.com')->first();
            if ($admin) {
                Mail::to($admin->email)->send(new NotificacionNuevoPaciente($admin, $paciente, $clinica, $perfilPacienteUrl));
            }

            // 10. Disparar evento y resetear formulario
            $this->dispatch('nuevoPaciente');
            $this->resetForm();
            $this->showModal = false;
            $this->resetPage();
        });
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

        // Normalizar nombres para evitar problemas con los directorios
        $nombreClinica = preg_replace('/\s+/', '_', trim($clinica->name));
        $nombrePaciente = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $paciente->apellidos));

        // Ruta base para la carpeta del paciente
        $pacienteFolder = $nombreClinica . '/pacientes/' . $nombrePaciente;

        // Lista de subcarpetas dentro de la carpeta del paciente
        $subFolders = ['imgEtapa', 'CBCT', 'archivoComplementarios', 'Rayos', 'Stripping'];

        // Verificar si la carpeta del paciente ya existe en la base de datos
        $carpetaPaciente = Carpeta::where('nombre', $nombrePaciente)
            ->whereHas('carpeta_id', function ($query) use ($nombreClinica) {
                $query->where('nombre', $nombreClinica);
            })->first();

        if (!$carpetaPaciente) {
            // Crear carpeta del paciente en el sistema de archivos si no existe
            if (!Storage::disk('clinicas')->exists($pacienteFolder)) {
                Storage::disk('clinicas')->makeDirectory($pacienteFolder);
            }

            // Registrar la carpeta del paciente en la base de datos
            $carpetaClinica = Carpeta::where('nombre', $nombreClinica)->first();
            $carpetaPaciente = Carpeta::create([
                'nombre' => $nombrePaciente,
                'carpeta_id' => $carpetaClinica->id ?? null, // Relación con la clínica
            ]);
        }

        // Crear subcarpetas dentro de la carpeta del paciente
        foreach ($subFolders as $subFolder) {
            $subFolderPath = $pacienteFolder . '/' . $subFolder;

            if (!Storage::disk('clinicas')->exists($subFolderPath)) {
                Storage::disk('clinicas')->makeDirectory($subFolderPath);
            }

            // Guardar subcarpeta en la base de datos si no existe
            Carpeta::firstOrCreate([
                'nombre' => $subFolder,
                'carpeta_id' => $carpetaPaciente->id, // Relación con la carpeta del paciente
            ]);
        }
    }

    // CAMBIAR ESTADO PACIENTE ETAPA
    public function estado($pacienteId, $newStatus)
    {
        // Encuentra al paciente y su etapa actual
        $paciente = Paciente::find($pacienteId);

        if (!$paciente) {
            session()->flash('error', 'Paciente no encontrado.');
            return;
        }

        $etapa = Etapa::where('paciente_id', $pacienteId)->first();

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
        // $clinica = Clinica::find($paciente->clinica_id);
        // if ($clinica && $clinica->email) {
        //     Mail::to($clinica->email)->send(new CambioEstado($paciente, $newStatus, $etapa));
        // }
    }

    public function toggleMenu($pacienteId)
    {
        $this->menuVisible = $this->menuVisible === $pacienteId ? null : $pacienteId;
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

    // public function toggleMenu($etapaId)
    // {
    //     $this->mostrarMenu[$etapaId] = isset($this->mostrarMenu[$etapaId]) ? !$this->mostrarMenu[$etapaId] : true;
    // }
}
