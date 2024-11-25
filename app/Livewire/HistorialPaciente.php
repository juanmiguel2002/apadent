<?php

namespace App\Livewire;

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
use App\Models\Fase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class HistorialPaciente extends Component
{
    use WithFileUploads;

    public $clinica, $paciente, $pacienteId;
    public $tratamiento, $tratamientos, $tratamientoId;
    public $etapa_paciente = [], $etapas;
    public $mensajes = [], $revision;
    public $selectedTratamiento, $selectedNewTratamiento, $tratId;
    public $showTratamientoModal = false, $mostrarMenu = [], $modalOpen = false, $documents = false;
    public $etapaId; // para guardar el ID de la etapa correspondiente
    public $archivo, $img = false, $completado;
    public $modalImg, $imagenes = [], $modalArchivo = false, $archivos = [];
    public $selectedEtapa, $documentacion;
    public $mostrarBotonNuevaEtapa = false, $ultimaEtapa;
    public $fases;

    public $statuses = [
        'En proceso' => 'bg-green-600',
        'Pausado' => 'bg-blue-600',
        'Finalizado' => 'bg-red-600',
        'Set Up' => 'bg-yellow-600'
    ];

    protected $rules = [
        'selectedNewTratamiento' => 'required|exists:tratamientos,id',
    ];

    public function mount($paciente, $tratamiento = null, $tratId = null)
    {
        $this->paciente = $paciente;
        $this->clinica = Clinica::find($this->paciente->clinica_id);
        $this->pacienteId = $this->paciente->id;
        $this->tratId = $tratId; // Tratamiento pasado por la URL (si existe)
        $this->tratamiento = $tratamiento; // Tratamiento pasado por la URL (si existe)

        // Cargar las etapas asociadas al tratamiento seleccionado (si existe un tratamiento)
        if($this->tratId){
            $this->loadFases($tratId);
        }

        $this->archivo = Archivo::where('etapa_id', $this->pacienteId)->where('tipo', 'zip')->get();
        // dd($this->archivo);
    }

    public function loadFases($trat = null)
    {
        $this->fases = Fase::where('trat_id', $this->tratId ? $this->tratId : $this->tratamientoId)
            ->whereHas('etapas') // Filtra las fases que tienen etapas asignadas
            ->get();
        if ($trat) {
            $this->etapas = Etapa::with(['fase', 'archivos', 'mensajes.user'])
            ->whereHas('fase.tratamiento', function ($query) use ($trat) {
                $query->where('trat_id', $trat);
            })
            ->get();
        } else {
            $this->tratId = null;
            $this->etapas = Etapa::with(['fase', 'archivos', 'mensajes.user'])
                ->whereHas('fase.tratamiento', function ($query) {
                    $query->where('id', $this->tratamientoId);
                })
                ->get();
        }
    }

    public function toggleAcordeon($faseName)
    {
        // Alternar la visibilidad del acordeón para la fase
        if (isset($this->mostrarMenu[$faseName])) {
            // Si ya está abierto, cerrarlo
            unset($this->mostrarMenu[$faseName]);
        } else {
            // Si no está abierto, abrirlo
            $this->mostrarMenu[$faseName] = true;
        }
    }

    public function render()
    {
        return view('livewire.historial-paciente');
    }

    // COMPRUEBA SI TIENE ARCHIVO UNA ETAPA
    public function tieneArchivos($etapaId, $archivo)
    {
        if($archivo){
           return Archivo::where('etapa_id', $etapaId)->where('tipo', 'zip')->exists();
        }
        return Archivo::where('etapa_id', $etapaId)->exists();
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
        $this->loadFases($this->tratId ? $this->tratId : $this->tratamientoId);
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
        $this->loadFases($this->tratId ? $this->tratId : $this->tratamientoId);

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
            $this->loadFases($this->tratId ? $this->tratId : $this->tratamientoId);

            Mail::to($this->clinica->email)->send(new NotificacionRevision($this->paciente, $etapa));

        }
    }

    // Nueva etapa
    public function nuevaEtapa()
    {
        // Buscar la fase asociada
        $fase = Fase::where('trat_id', $this->tratId ? $this->tratId : $this->tratamientoId)
            ->first();

        // Obtener el número de la última etapa asociada a esta fase
        $ultimoNumero = $fase->etapas()->count() + 1;

        // Crear la nueva etapa
        Etapa::create([
            'name' => "Etapa $ultimoNumero",
            'fecha_ini' => now(),
            'status' => 'Set Up', // Estado inicial
            'fases_id' => $fase->id,
        ]);

        // Emitir un evento para actualizar la vista
        $this->dispatch('etapa');
        $this->loadFases($this->tratId ? $this->tratId : $this->tratamientoId);
    }

    // GESTIÓN NEW TRATAMIENTO
    public function showTratamientosModal()
    {
        $this->showTratamientoModal = true;
        $this->reset(['selectedTratamiento']);
    }

    public function saveTratamiento()
    {
        // Validar que se haya seleccionado un tratamiento
        $this->validate([
            'selectedNewTratamiento' => 'required|exists:tratamientos,id',
        ], [
            'selectedNewTratamiento' => 'Has selecionado un tratamiento que esta asignado ya'
        ]);

        try {
            // Verificar si el tratamiento ya está asociado con el paciente
            $existingTratamiento = PacienteTrat::where('paciente_id', $this->pacienteId)
                ->where('trat_id', $this->selectedNewTratamiento)
                ->first();

            if ($existingTratamiento) {
                $this->dispatch('error', 'El tratamiento ya está asociado al paciente.');
                return;
            }

            // Crear la relación entre el paciente y el tratamiento
            $pacienteTrat = PacienteTrat::create([
                'paciente_id' => $this->pacienteId,
                'trat_id' => $this->selectedNewTratamiento,
            ]);

            // Obtener las etapas del tratamiento seleccionado
            $etapas = TratamientoEtapa::where('trat_id', $this->selectedNewTratamiento)->get();

            // Asociar cada etapa con el paciente en `paciente_etapas`
            foreach ($etapas as $etapa) {
                PacienteEtapas::create([
                    'paciente_id' => $this->pacienteId,
                    'etapa_id' => $etapa->etapa_id,
                    'status' => 'Set Up', // Estado inicial
                    'fecha_ini' => now(), // Fecha de inicio actual
                ]);
            }

            // Resetear el tratamiento seleccionado y cerrar el modal
            $this->reset('selectedNewTratamiento');
            $this->closeModal();
            $this->loadFases($this->selectedNewTratamiento);
            // Emitir un evento para actualizar la lista de tratamientos en la vista principal si es necesario
            $this->dispatch('tratamientoAsignado', 'Tratamiento asignado.');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Ocurrió un error al guardar el tratamiento.');
        }
    }

    // Nueva Documentación
    public function showDocumentacionModal(){
        $this->documents = true;
    }

    public function saveDocumentacion($tratId, $etapaId){

    }

    // GESTIONAR IMÁGENES ETAPA PACIENTE TRATAMIENTO
    public function verImg($etapaId){
        return redirect()->route('imagenes.ver', ['paciente' => $this->pacienteId, 'etapa' => $etapaId]);
    }

    public function showModalImg(){
        $this->modalImg = true;
    }

    public function saveImg($etapaId){

        $this->validate([
            'imagenes.*' => 'required|image',
        ]);

        $etapa = Etapa::find($etapaId);
        $clinicaName = preg_replace('/\s+/', '_', trim(Auth::user()->clinicas->first()->name));
        $pacienteName = preg_replace('/\s+/', '_', trim($this->paciente->name . ' ' . $this->paciente->apellidos));
        $pacienteFolder = $clinicaName . '/pacientes/' . $pacienteName;

        // Subir múltiples imágenes del paciente, si existen
        if ($this->imagenes && is_array($this->imagenes)) {
            foreach ($this->imagenes as $key => $imagen) {
                $extension = $imagen->getClientOriginalExtension();
                $fileName = $etapa->name . "_" . $key . '.' . $extension;
                $path = $imagen->storeAs($pacienteFolder . '/imgEtapa', $fileName, 'clinicas');

                // Guardar la ruta de la imagen en la tabla de archivos
                $archivo = Archivo::create([
                    'ruta' => $path,
                    'tipo' => $extension,
                    'paciente_id' => $this->pacienteId,
                    'paciente_etapa_id' => $etapaId,
                ]);
            }
            $archivo->save();
        }
        $this->modalImg = false;
    }

    // GESTIONAR ARCHIVOS ETAPA PACIENTE TRATAMIENTO
    public function showModalArchivo(){
        $this->modalArchivo = true;
    }

    public function saveArchivos($etapaId){

        $etapa = Etapa::find($etapaId);
        $clinicaName = preg_replace('/\s+/', '_', trim(Auth::user()->clinicas->first()->name));
        $pacienteName = preg_replace('/\s+/', '_', trim($this->paciente->name . ' ' . $this->paciente->apellidos));
        $pacienteFolder = $clinicaName . '/pacientes/' . $pacienteName;

        $this->validate([
            'archivos.*' => 'required|file',
        ]);

        // Subir múltiples imágenes del paciente, si existen
        if ($this->archivos && is_array($this->archivos)) {
            foreach ($this->archivos as $key => $archivo) {
                $extension = $archivo->getClientOriginalExtension();
                $fileName = $etapa->name."_" . $key . '.' . $extension;
                $path = $archivo->storeAs($pacienteFolder . '/archivoEtapa', $fileName, 'clinicas');

                // Guardar la ruta de la imagen en la tabla de archivos
                $archivo = Archivos::create([
                    'ruta' => $path,
                    'tipo' => $extension,
                    'paciente_id' => $this->pacienteId,
                    'paciente_etapa_id' => $etapaId,
                ]);
            }
            $archivo->save();
        }
        $this->modalArchivo = false;
    }

    public function closeModal(){
        if($this->documents) {
            $this->documents = false;
        }elseif ($this->modalOpen){
            $this->modalOpen = false;
            $this->reset(['revision']);
        }elseif($this->modalImg){
            $this->modalImg = false;
        }elseif($this->modalArchivo){
            $this->modalArchivo = false;
        }else{
            $this->showTratamientoModal = false;
            // $this->reset(['selectedNewTratamiento']);
        }
        $this->loadFases($this->selectedTratamiento);
    }

    public function toggleMenu($etapaId)
    {
        $this->mostrarMenu[$etapaId] = isset($this->mostrarMenu[$etapaId]) ? !$this->mostrarMenu[$etapaId] : true;
    }
}
