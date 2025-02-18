<?php

namespace App\Livewire\Pacientes;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Mail\CambioEstado;
use App\Models\Clinica;
use App\Models\Etapa;
use App\Models\Mensaje;
use App\Models\Archivo;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;
use App\Mail\NotificacionMensaje;
use App\Mail\NotificacionRevision;
use App\Models\Carpeta;
use App\Models\Fase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HistorialPaciente extends Component
{
    use WithFileUploads;

    public $clinica, $paciente, $pacienteId;
    public $tratamiento, $tratamientos, $tratamientoId;
    public $etapas, $mensajes = [], $revision;
    public $selectedTratamiento, $selectedNewTratamiento, $tratId;
    public $showTratamientoModal = false, $mostrarMenu = [], $modalOpen = false, $documents = false;
    public $etapaId; // para guardar el ID de la etapa correspondiente
    public $archivo, $img = false;
    public $modalImg, $imagenes = [];
    public $modalArchivo = false, $archivos = [];

    public $statuses = [
        'En proceso' => 'bg-green-600',
        'Pausado' => 'bg-blue-600',
        'Finalizado' => 'bg-red-600',
        'Set Up' => 'bg-yellow-600'
    ];

    protected $rules = [
        'selectedNewTratamiento' => 'required|exists:tratamientos,id',
    ];

    public function mount($paciente, $tratId = null)
    {
        $this->paciente = $paciente;
        $this->clinica = Clinica::find($this->paciente->clinica_id);
        $this->pacienteId = $this->paciente->id;

        $this->tratId = $tratId;// comprobar si existe tratamiento asignado por url
        // $this->tratamiento = $tratamiento;//se pasa el tratamiento seleccionado por url

        // Cargar las fases del tratamiento seleccionado (si existe un tratamiento)
        if ($this->tratId) {
            $this->loadEtapas($this->tratId);
        }else{
            $this->loadEtapas($this->tratamientoId);
        }

        // Cargar archivos relacionados con las etapas del paciente
        $this->archivo = Archivo::whereHas('etapas', function ($query) {
            $query->where('paciente_id', $this->pacienteId);
        })->where('extension', 'zip')->get();
    }

    public function loadEtapas($tratamientoId)
    {
        // Cargar las etapas específicas para la fase activa
        $this->etapas = Etapa::whereHas('fase', function ($query) use ($tratamientoId) {
            $query->where('trat_id', $tratamientoId);
        })
        ->where('paciente_id', $this->pacienteId)
        ->with('fase')
        ->get();

    }

    public function render()
    {
        return view('livewire.pacientes.historial-paciente');
    }

    // COMPRUEBA SI TIENE ARCHIVO UNA ETAPA
    public function tieneArchivos($etapaId, $archivo = false,$tipo)
    {
        if($archivo){
           return Archivo::where('etapa_id', $etapaId)->where('extension', 'zip')->exists();
        }
        return Archivo::where('etapa_id', $etapaId)
                      ->where('tipo', $tipo)
                      ->exists();
    }

    // ENVIAR MENSAJE TRATAMIENTO ETAPA PACIENTE
    public function enviarMensaje($etapaId)
    {
        // Validar que el mensaje no esté vacío
        $mensaje = $this->mensajes[$etapaId] ?? '';

        $this->validate([
            'mensajes.' . $etapaId => 'required|min:3|max:255',
        ], [
            'mensajes.' . $etapaId . '.required' => 'El mensaje es obligatorio.',
            'mensajes.' . $etapaId . '.min' => 'El mensaje debe tener al menos 3 caracteres.',
            'mensajes.' . $etapaId . '.max' => 'El mensaje no puede tener más de 255 caracteres.',
        ]);

        // Crear el mensaje
        Mensaje::create([
            'user_id' => auth()->id(),
            'mensaje' => $mensaje,
            'etapa_id' => $etapaId,
        ]);

        // Limpiar el campo de mensaje
        $this->mensajes[$etapaId] = '';
        $this->loadEtapas($this->tratId ? $this->tratId : $this->tratamientoId);
        $this->dispatch('mensaje');

        $etapa = Etapa::find($etapaId);
        $trat = Tratamiento::find($this->tratId ? $this->tratId : $this->tratamientoId);

        Mail::to($this->clinica->email)->send(new NotificacionMensaje($this->paciente, $etapa, $trat, $mensaje));

    }

    // CAMBIO ESTADO PACIENTE ETAPA
    public function estado($etapaId, $newStatus)
    {
        $etapa = Etapa::find($etapaId);

        if($newStatus === 'Finalizado'){
            $etapa->update(['status' => $newStatus, 'fecha_fin' => now()]);
        }else{
            $etapa->status = $newStatus;
        }
        $etapa->save();
        $this->mostrarMenu = false; // Cerrar el menú
        $this->dispatch('estadoActualizado');
        $this->loadEtapas($this->selectedTratamiento);

        // Enviar email a la clínica
        $etapa = Etapa::find($etapaId);
        $trat = Tratamiento::find($this->tratId ? $this->tratId : $this->tratamientoId);

        if ($this->clinica && $this->clinica->email) {
            Mail::to($this->clinica->email)->send(new CambioEstado($this->paciente, $newStatus, $etapa, $trat));
        }
    }

    // REVISIÓN FECHA
    public function abrirModalRevision($id)
    {
        $this->etapaId = $id;
        $this->modalOpen = true;
    }

    public function revisionEtapa(){

        // Actualizar la revisión en la tabla etapas
        $etapa = Etapa::find($this->etapaId);

        if ($etapa) {
            $etapa->revision = $this->revision; // Actualiza el campo 'revision'
            $etapa->save(); // Guarda los cambios

            $this->dispatch('revision');
            $this->modalOpen = false;
            $this->loadEtapas($this->selectedTratamiento);

            Mail::to($this->clinica->email)->send(new NotificacionRevision($this->paciente, $etapa, $this->clinica));
        }
    }

    // Nueva Etapa
    public function nuevaEtapa($tratamientoId)
    {
        $fase = Fase::where('trat_id', $tratamientoId)->first();

        if (!$tratamientoId) {
            session()->flash('error', 'La fase especificada no existe.');

            return;
        }

        // Obtener el número consecutivo de la nueva etapa
        $numeroEtapa = Etapa::where('trat_id', $tratamientoId)->where('paciente_id', $this->pacienteId)->count() + 1;
        $nombreEtapa = "Etapa " . $numeroEtapa;

        // Crear una nueva etapa
        Etapa::create([
            'name' => $nombreEtapa, // Nombre consecutivo
            'fecha_ini' => now(),
            'status' => 'Set Up', // Status inicial de la etapa
            'trat_id' => $tratamientoId,
            'fase_id' => $fase->id, // Relacionar con la fase seleccionada
            'paciente_id' => $this->pacienteId, // Relacionar con el paciente seleccionado
        ]);

        $this->dispatch('etapa');
        // Recargar las etapas para reflejar los cambios
        $this->loadEtapas($tratamientoId);
    }

    // GESTIÓN NEW TRATAMIENTO
    public function showTratModal()
    {
        $this->showTratamientoModal = true;
        $this->tratamientos = Tratamiento::all();
    }

    public function saveTratamiento()
    {
        // Validar que se haya seleccionado un tratamiento válido
        $this->validate([
            'selectedNewTratamiento' => 'required|exists:tratamientos,id',
        ], [
            'selectedNewTratamiento.required' => 'Debes seleccionar un tratamiento.',
            'selectedNewTratamiento.exists' => 'El tratamiento seleccionado no es válido.',
        ]);

        try {
            DB::transaction(function () {
                // Verificar si el tratamiento ya está asociado con el paciente
                $existe = PacienteTrat::where('paciente_id', $this->pacienteId)
                    ->where('trat_id', $this->selectedNewTratamiento)
                    ->exists();

                if ($existe) {
                    throw new \Exception('El tratamiento ya está asociado al paciente.');
                }

                // Asociar el tratamiento al paciente
                PacienteTrat::create([
                    'paciente_id' => $this->pacienteId,
                    'trat_id' => $this->selectedNewTratamiento,
                ]);

                // Obtener todas las fases del tratamiento seleccionado
                $fases = Fase::where('trat_id', $this->selectedNewTratamiento)->get();

                foreach ($fases as $fase) {
                    // Crear una etapa inicial para cada fase
                    Etapa::create([
                        'fase_id' => $fase->id,
                        'paciente_id' => $this->pacienteId,
                        'name' => 'Inicio',
                        'status' => 'Set Up',
                        'fecha_ini' => now(),
                    ]);
                }
            });

            // Resetear el tratamiento seleccionado y cerrar el modal
            $this->reset('selectedNewTratamiento');
            $this->closeModal();
            $this->mount($this->paciente, null, $this->selectedNewTratamiento);

            // Emitir un evento para actualizar la lista de tratamientos en la vista
            $this->dispatch('tratamientoAsignado', 'Tratamiento asignado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Tratamiento ya asignado');
        }
    }

    // Nueva Documentación
    public function abrirModalDocumentacion()
    {
        $this->dispatch('abrirModalDocumentacion', $this->paciente->id, $this->tratId ? $this->tratId : null);
    }
    // GESTIONAR IMÁGENES ETAPA PACIENTE TRATAMIENTO
    public function showModalImg($etapaId){
        $this->etapaId = $etapaId;
        $this->modalImg = true;
    }

    public function saveImg(){

        $this->validate([
            'imagenes.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ], [
            'imagenes.*' => 'Solo se admiten imágenes'
        ]);

        $etapa = Etapa::find($this->etapaId);
        $clinicaName = preg_replace('/\s+/', '_', trim(Auth::user()->clinicas->first()->name));
        $pacienteName = preg_replace('/\s+/', '_', trim($this->paciente->name . ' ' . $this->paciente->apellidos));
        $pacienteFolder = $clinicaName . '/pacientes/' . $pacienteName;

        $carpetaPaciente = Carpeta::where('nombre', preg_replace('/\s+/', '_', trim($this->paciente->name . ' ' . $this->paciente->apellidos)))
        ->whereHas('parent', function ($query) {
            $query->where('nombre', 'pacientes');
        })->first();

        $carpeta = Carpeta::where('nombre', 'imgEtapa')
                    ->where('carpeta_id', $carpetaPaciente->id)
                    ->first();

        // Subir múltiples imágenes del paciente, si existen
        if ($this->imagenes && is_array($this->imagenes)) {
            foreach ($this->imagenes as $key => $imagen) {
                $extension = $imagen->getClientOriginalExtension();
                $fileName = $etapa->name . "_" . $key . '.' . $extension;
                $path = $imagen->storeAs($pacienteFolder . '/imgEtapa', $fileName, 'clinicas');
                // Extraer el nombre del archivo sin la extensión
                $Name = pathinfo($fileName, PATHINFO_FILENAME);
                // Guardar la ruta de la imagen en la tabla de archivos
                Archivo::create([
                    'name' => $Name,
                    'ruta' => $path,
                    'tipo' => 'imgetapa',
                    'extension'=>$extension,
                    'etapa_id' => $this->etapaId,
                    'carpeta_id' => $carpeta->id,

                ]);
            }
        }
        $this->modalImg = false;
        return redirect()->route('imagenes.ver', ['paciente' => $this->pacienteId, 'etapa' => $this->etapaId]);
    }

    // GESTIONAR ARCHIVOS ETAPA PACIENTE TRATAMIENTO
    // public function showModalArchivo(){
    //     $this->modalArchivo = true;
    // }

    // public function saveArchivos($etapaId){

    //     $etapa = Etapa::find($etapaId);
    //     $clinicaName = preg_replace('/\s+/', '_', trim(Auth::user()->clinicas->first()->name));
    //     $pacienteName = preg_replace('/\s+/', '_', trim($this->paciente->name . ' ' . $this->paciente->apellidos));
    //     $pacienteFolder = $clinicaName . '/pacientes/' . $pacienteName;

    //     $this->validate([
    //         'archivos.*' => 'required|file',
    //     ]);

    //     // Subir múltiples imágenes del paciente, si existen
    //     if ($this->archivos && is_array($this->archivos)) {
    //         foreach ($this->archivos as $key => $archivo) {
    //             $extension = $archivo->getClientOriginalExtension();
    //             $fileName = $etapa->name."_" . $key . '.' . $extension;
    //             $path = $archivo->storeAs($pacienteFolder . '/archivoEtapa', $fileName, 'clinicas');

    //             // Guardar la ruta de la imagen en la tabla de archivos
    //             $archivo = Archivo::create([
    //                 'ruta' => $path,
    //                 'tipo' => $extension,
    //                 'paciente_id' => $this->pacienteId,
    //                 'paciente_etapa_id' => $etapaId,
    //             ]);
    //         }
    //         $archivo->save();
    //     }
    //     $this->modalArchivo = false;
    // }

    public function closeModal(){
        if($this->documents) {
            $this->documents = false;
            $this->reset();
        }elseif ($this->modalOpen){
            $this->modalOpen = false;
            $this->reset(['revision']);
        }elseif($this->modalImg){
            $this->modalImg = false;
        }elseif($this->modalArchivo){
            $this->modalArchivo = false;
        }else {
            $this->showTratamientoModal = false;
            // $this->reset(['selectedNewTratamiento']);
        }
        $this->loadEtapas($this->selectedTratamiento);
    }

    public function toggleMenu($etapaId)
    {
        $this->mostrarMenu[$etapaId] = isset($this->mostrarMenu[$etapaId]) ? !$this->mostrarMenu[$etapaId] : true;
    }
}
