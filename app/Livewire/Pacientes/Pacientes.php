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
use Illuminate\Support\Str;

class Pacientes extends Component
{
    use WithFileUploads, WithPagination;

    public $tratamientos, $clinica_id, $paciente, $paciente_id;
    // añadir paciente
    public $num_paciente, $name, $apellidos, $email, $telefono, $fecha_nacimiento;
    public $observacion, $obser_cbct, $img_paciente;

    public $showModal = false;
    public $menuVisible = null;

    public $imagenes = [], $cbct = [], $rayos = []; //archivos que del paciente
    public $selectedTratamiento, $status = "Set Up", $activo = false;

    public $search = '';
    public $ordenar = '';
    public $perPage = 25; //Para filtrar cuando se ve
    public $clinicas, $clinicaSelected;

    public $pacienteFolder;

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
        'img_paciente' => 'nullable|image',
        'rayos.*' => 'nullable|image',
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
                'clinica_id' => $this->clinica_id ? $this->clinica_id : Auth::user()->clinicas->first()->id,
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
                        'trat_id' => $this->selectedTratamiento,
                    ],
                    [
                        'name' => 'Inicio',
                        'fecha_ini' => now(),
                        'status' => 'Set Up',
                    ]
                );
            }

            // 4. Crear carpetas para el paciente
            $pacienteFolder = $this->createPacienteFolders($paciente);

            // 5. Subir la foto del paciente (si existe)
            if ($this->img_paciente) {
                $extension = $this->img_paciente->getClientOriginalExtension();
                $fileName = 'foto_'.$paciente->name.'.' . $extension;
                $path = $this->img_paciente->storeAs($pacienteFolder . '/fotoPaciente', $fileName, 'public');

                $paciente->url_img = $path;
                $paciente->save();
                unlink($this->img_paciente->getPathname());
            }

            // 6. Subir archivos y asociarlos
            $this->updatedArchivos($paciente, $pacienteFolder, $etapa);

            // 7. Disparar evento y resetear formulario
            $this->showModal = false;
            $this->dispatch('nuevoPaciente');
            $this->dispatch('recargar-pagina');

            // 8. Enviar email de notificación a la clínica y al admin
            $clinica = Clinica::find($paciente->clinica_id);
            if ($clinica && $clinica->email) {
                Mail::to($clinica->email)->send(new NotificacionNuevoPaciente($clinica, $paciente, null, route('pacientes-show', $paciente->id)));
            }
        });
    }

    /**
     * Crea la carpeta del paciente dentro de la clínica y sus subcarpetas.
     */
    public function createPacienteFolders($paciente)
    {
        if($this->clinica_id){
            $clinica = Clinica::find($this->clinica_id);
        }else {
            $clinica = Clinica::find(Auth::user()->clinicas->first()->id);
        }
        if (!$clinica && !$this->clinica_id) {
            $this->dispatch('error', "No se encontró ninguna clínica asociada.");
        }

        // Normalizar nombres
        $nombreClinica = preg_replace('/\s+/', '_', trim($clinica->name));
        $nombrePaciente = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $paciente->apellidos));

        // Ruta de la carpeta del paciente
        $pacienteFolder = "{$nombreClinica}/pacientes/{$nombrePaciente}";

        // Crear carpeta en sistema de archivos si no existe
        if (!Storage::disk('clinicas')->exists($pacienteFolder)) {
            Storage::disk('clinicas')->makeDirectory($pacienteFolder);
        }

        // Guardar estructura en la base de datos
        $carpetaClinica = Carpeta::firstOrCreate(['nombre' => $nombreClinica, 'carpeta_id' => null]);
        $carpetaPacientes = Carpeta::firstOrCreate(['nombre' => 'pacientes', 'carpeta_id' => $carpetaClinica->id, 'clinica_id' => $clinica->id]);
        $carpetaPaciente = Carpeta::firstOrCreate(['nombre' => $nombrePaciente, 'carpeta_id' => $carpetaPacientes->id, 'clinica_id' => $clinica->id]);

        // Subcarpetas
        $subFolders = ['imgEtapa', 'CBCT', 'archivoComplementarios', 'Rayos', 'Stripping'];
        foreach ($subFolders as $subFolder) {
            $subFolderPath = "{$pacienteFolder}/{$subFolder}";
            if (!Storage::disk('clinicas')->exists($subFolderPath)) {
                Storage::disk('clinicas')->makeDirectory($subFolderPath);
            }
            Carpeta::firstOrCreate(['nombre' => $subFolder, 'carpeta_id' => $carpetaPaciente->id, 'clinica_id' => $clinica->id]);
        }

        return $pacienteFolder;
    }

    /**
     * Guarda los archivos subidos en la base de datos y en el almacenamiento.
     */
    public function updatedArchivos($paciente, $pacienteFolder, $etapa)
    {
        $carpetaPaciente = Carpeta::where('nombre', preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $paciente->apellidos)))
            ->whereHas('parent', function ($query) {
                $query->where('nombre', 'pacientes');
            })->first();

        if (!$carpetaPaciente) {
            session()->flash('error', 'Carpeta del paciente no encontrada.');
            return;
        }

        $subCarpetas = [
            'imgEtapa' => $this->imagenes,
            'CBCT' => $this->cbct,
            'Rayos' => $this->rayos,
        ];

        foreach ($subCarpetas as $nombreCarpeta => $archivos) {
            if ($archivos && is_array($archivos)) {
                $carpeta = Carpeta::where('nombre', $nombreCarpeta)
                    ->where('carpeta_id', $carpetaPaciente->id)
                    ->first();

                if (!$carpeta) {
                    session()->flash('error', "Carpeta {$nombreCarpeta} no encontrada.");
                    continue;
                }

                foreach ($archivos as $key => $archivo) {
                    $extension = $archivo->getClientOriginalExtension();

                    // Asegurar que el nombre de la carpeta sea seguro
                    // $safeFolderName = Str::slug($nombreCarpeta, '_');

                    // Generar el nombre del archivo de manera segura
                    $fileName = "{$etapa->name}_" . ($key + 1) . '.' . $extension;

                    // Asegurar que el nombre no tenga caracteres extraños
                    $fileName = preg_replace('/[^\w.-]/', '_', $fileName);

                    // Guardar el archivo en la carpeta del paciente
                    $path = $archivo->storeAs("{$pacienteFolder}/{$nombreCarpeta}", $fileName, 'clinicas');
                    // Guardar en la base de datos solo el nombre sin extensión
                    Archivo::create([
                        'name' => pathinfo($fileName, PATHINFO_FILENAME),
                        'ruta' => $path,
                        'tipo' => strtolower($nombreCarpeta),
                        'extension' => $extension,
                        'etapa_id' => $etapa->id,
                        'carpeta_id' => $carpeta->id,
                        'paciente_id' => $paciente->id,
                    ]);
                    unlink($archivo->getPathname());
                }
            }
        }
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
}
