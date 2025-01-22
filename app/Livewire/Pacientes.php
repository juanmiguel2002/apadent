<?php

namespace App\Livewire;

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
use App\Models\Clinica;
use App\Models\Etapa;
use App\Models\Fase;
use App\Models\Paciente;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;

class Pacientes extends Component
{
    use WithFileUploads, WithPagination;
    public $menuVisible = null;
    public $tratamientos, $clinica_id, $paciente, $paciente_id;
    public $num_paciente, $name, $apellidos, $email, $telefono, $fecha_nacimiento;
    public $observacion, $obser_cbct;
    public $showModal = false, $mostrarMenu = [];
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
        // Configuración de la ordenación
        $orderByColumn = $this->ordenar === 'recientes' ? 'pacientes.created_at' : ($this->ordenar === 'name' ? 'pacientes.name' : 'pacientes.id');
        $orderByDirection = $this->ordenar === 'recientes' ? 'desc' : 'asc';

        // Consulta optimizada
        $pacientes = Paciente::with([
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
        ->where('activo', $this->activo ? 0 : 1) // Filtrar por pacientes activos o inactivos
        ->orderBy($orderByColumn, $orderByDirection) // Orden dinámico
        ->paginate($this->perPage);

        // dd($pacientes);
        return view('livewire.pacientes', [
            'pacientes' => $pacientes,
        ]);
    }


    public function render2()
    {
        // Definir columna y dirección de ordenación por defecto
        $orderByColumn = 'id';
        $orderByDirection = 'asc';

        // Determinar columna de ordenación basada en la selección del usuario
        if ($this->ordenar === 'recientes') {
            $orderByColumn = 'created_at';
            $orderByDirection = 'desc';
        } elseif ($this->ordenar === 'name') {
            $orderByColumn = 'name';
            $orderByDirection = 'asc';
        }

        // Consulta optimizada para obtener pacientes con sus etapas relacionadas
        $pacientes = Paciente::with([
            'tratamientos.fases.etapas' => function ($query) {
                $query->where('etapas.status', '<>', 'Finalizado') // Mostrar solo etapas no finalizadas
                    ->orWhere('etapas.status', '=', 'Finalizado');
            },
            'clinicas',
        ])
        ->where(function ($query) { // Búsqueda por nombre, apellidos o teléfono
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                ->orWhere('telefono', 'like', '%' . $this->search . '%');
        })
        ->where('activo', $this->activo ? 0 : 1) // Filtrar por pacientes activos o inactivos
        ->orderBy($orderByColumn, $orderByDirection) // Ordenar según selección
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

    // public function save()
    // {
    //     $this->validate();

    //     // 1. Crear el paciente
    //     $paciente = Paciente::create([
    //         'num_paciente' => $this->num_paciente,
    //         'name' => $this->name,
    //         'apellidos' => $this->apellidos,
    //         'fecha_nacimiento' => $this->fecha_nacimiento,
    //         'email' => $this->email,
    //         'telefono' => $this->telefono,
    //         'observacion' => $this->observacion,
    //         'obser_cbct' => $this->obser_cbct,
    //         'clinica_id' => Auth::user()->clinicas->first()->id,
    //     ]);

    //     // 2. Asociar tratamiento al paciente
    //     PacienteTrat::create([
    //         'paciente_id' => $paciente->id,
    //         'trat_id' => $this->selectedTratamiento,
    //     ]);

    //     // 3. Obtener las fases del tratamiento seleccionado y asociarlas al paciente
    //     $fase = Fase::where('trat_id', $this->selectedTratamiento)->get();

    //     $etapa = Etapa::create([
    //         'name' => 'Inicio',
    //         'fecha_ini' => now(),
    //         'status' => 'Finalizado', // Por defecto, las etapas están en estado "Set Up" Preguntar?
    //         'fases_id' => $fase->first()->id,
    //         'paciente_id' => $paciente->id,
    //     ]);

    //     // 4. Crear carpetas para el paciente (si es necesario)
    //     $this->createPacienteFolders($paciente->id);

    //     // Generamos los nombres de la clínica i el paciente
    //     $clinicaName = preg_replace('/\s+/', '_', trim(Auth::user()->clinicas->first()->name));
    //     $pacienteName = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $paciente->apellidos));
    //     $pacienteFolder = $clinicaName . '/pacientes/' . $pacienteName;

    //     // 5. Subir la foto del paciente (si existe)
    //     $paciente->refresh();
    //     if ($this->img_paciente) {
    //         $extension = $this->img_paciente->getClientOriginalExtension();
    //         $fileName = $pacienteName . '.' . $extension;
    //         $path = $this->img_paciente->storeAs($pacienteFolder . '/fotoPaciente', $fileName ,'public');

    //         $paciente->url_img = $path;
    //         $paciente->save();
    //     }

    //     // Subir múltiples imágenes del paciente, si existen
    //     if ($this->imagenes && is_array($this->imagenes)) {
    //         foreach ($this->imagenes as $key => $imagen) {
    //             $extension = $imagen->getClientOriginalExtension();
    //             $fileName = "Etapa_". $etapa->name .'_'. $key.'.' . $extension;
    //             $path = $imagen->storeAs($pacienteFolder . '/imgEtapa', $fileName, 'clinicas');

    //             // Guardar la ruta de la imagen en la tabla de archivos
    //             $archivo = Archivo::create([
    //                 'ruta' => $path,
    //                 'tipo' => $extension,
    //                 'etapa_id' => $etapa->id,
    //             ]);
    //         }
    //         $archivo->save();
    //     }

    //     // Subir múltiples CBCT, si existen
    //     if ($this->cbct && is_array($this->cbct)) {
    //         foreach ($this->cbct as $cbctFile) {
    //             $path = $cbctFile->store($pacienteFolder . '/CBCT', 'clinicas');
    //             $extension = $cbctFile->getClientOriginalExtension();

    //             // Guardar la ruta del CBCT en la tabla de archivos
    //             Archivo::create([
    //                 'ruta' => $path,
    //                 'tipo' => $extension,
    //                 'etapa_id' => $etapa->id,
    //             ]);
    //         }
    //     }

    //     // Enviar email a la clínica
    //     $clinica = Clinica::find($paciente->clinica_id);
    //     if ($clinica && $clinica->email) {
    //         Mail::to($clinica->email)->send(new NotificacionNuevoPaciente($paciente));
    //     }

    //     //Disparar evento y resetear formulario
    //     $this->dispatch('nuevoPaciente');
    //     $this->resetForm();
    //     $this->showModal = false;
    //     $this->resetPage();
    // }
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
                        'status' => 'Finalizado',
                        'fecha_fin' => now(),
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
            }else{
                $paciente->save(['url_img' => storage_path('recursos/imagenes/foto_perfil.jpg')]);
            }

            // 6. Subir múltiples imágenes del paciente (asociadas a la primera etapa)
            if ($this->imagenes && is_array($this->imagenes)) {
                foreach ($this->imagenes as $key => $imagen) {
                    $extension = $imagen->getClientOriginalExtension();
                    $fileName = "Imagen_" . $key . '.' . $extension;//nombre del archivo
                    $path = $imagen->storeAs($pacienteFolder . '/imgEtapa', $fileName, 'clinicas');

                    Archivo::create([
                        'ruta' => $path,
                        'tipo' => $extension,
                        'etapa_id' => $etapa->id, // Asociar a la primera etapa creada
                    ]);
                }
            }

            // 7. Subir múltiples CBCT del paciente
            if ($this->cbct && is_array($this->cbct)) {
                foreach ($this->cbct as $cbctFile) {
                    $extension = $cbctFile->getClientOriginalExtension();
                    $path = $cbctFile->store($pacienteFolder . '/CBCT', 'clinicas');

                    Archivo::create([
                        'ruta' => $path,
                        'tipo' => $extension,
                        'etapa_id' => Etapa::where('fase_id', $fases->first()->id)
                                        ->where('paciente_id', $paciente->id)
                                        ->first()->id,
                    ]);
                }
            }

            // 8. Enviar email de notificación a la clínica
            $clinica = Clinica::find($paciente->clinica_id);
            if ($clinica && $clinica->email) {
                Mail::to($clinica->email)->send(new NotificacionNuevoPaciente($paciente));
            }

            // 9. Disparar evento y resetear formulario
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

        // Normalizar el nombre de la clínica y del paciente para evitar problemas con los nombres de directorios
        $nombreClinica = preg_replace('/\s+/', '_', trim($clinica->name));
        $nombrePaciente = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $paciente->apellidos));

        // Ruta base para la carpeta de la clínica y del paciente
        // $clinicaFolder = 'clinicas/' . $nombreClinica;
        $pacienteFolder = $nombreClinica . '/pacientes/' . $nombrePaciente;

        // Lista de subcarpetas que deseas crear dentro de la carpeta del paciente
        $subFolders = ['imgEtapa', 'CBCT', 'archivoEtapa', 'Stripping']; // imgPaciente se a eliminado

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
        /*
        $clinica = Clinica::find($paciente->clinica_id);
        if ($clinica && $clinica->email) {
            Mail::to($clinica->email)->send(new CambioEstado($paciente, $newStatus, $etapa));
        }
        */
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
