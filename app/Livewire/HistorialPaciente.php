<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Mail\CambioEstado;
use App\Models\Etapa;
use App\Models\Mensaje;
use App\Models\PacienteEtapas;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;
use App\Models\TratamientoEtapa;
use App\Models\Archivos;
use App\Mail\NotificacionMensaje;
use App\Mail\NotificacionRevision;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class HistorialPaciente extends Component
{
    use WithFileUploads;

    public $paciente, $pacienteId;
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


    public $statuses = [
        'En proceso' => 'bg-green-600',
        'Pausado' => 'bg-blue-600',
        'Finalizado' => 'bg-red-600',
        'Set Up' => 'bg-yellow-600'
    ];

    public function mount($paciente, $tratamiento = null, $tratId = null)
    {
        $this->paciente = $paciente;
        $this->pacienteId = $this->paciente->id;
        $this->tratId = $tratId; // Tratamiento pasado por la URL (si existe)
        $this->tratamiento = $tratamiento; // Tratamiento pasado por la URL (si existe)

        // Cargar las etapas asociadas al tratamiento seleccionado (si existe un tratamiento)
        if($this->tratId){
            $this->loadEtapas($tratId);
        }

        $this->archivo = Archivos::where('paciente_id', $this->pacienteId)->where('tipo', 'zip')->get();
        $this->tratamientos = Tratamiento::all();
        $this->verificarUltimaEtapa();
    }

    public function loadEtapas($trat = null)
    {
        if ($trat) {
            $this->etapas = PacienteEtapas::with(['etapa', 'mensajes.user'])
                ->where('paciente_id', $this->pacienteId)
                ->whereHas('etapa.tratamientos', function ($query) {
                    $query->where('tratamientos.id', $this->tratId);
                })
                ->get();
        } else {
            $this->tratId = null;
            $this->etapas = PacienteEtapas::with(['etapa', 'mensajes.user'])
                ->where('paciente_id', $this->pacienteId)
                ->whereHas('etapa.tratamientos', function ($query) {
                    $query->where('tratamientos.id', $this->tratamientoId);
                })
                ->get();
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
           return Archivos::where('paciente_etapa_id', $etapaId)->where('tipo', 'zip')->exists();
        }
        return Archivos::where('paciente_etapa_id', $etapaId)->exists();
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

        $paciente_trat =  PacienteTrat::where('paciente_id', $this->pacienteId)
            ->where('trat_id', $this->tratamientoId)
            ->first();

            // Crear el mensaje
        $Tmensaje = Mensaje::create([
            'user_id' => auth()->id(),
            'mensaje' => $mensaje,
            'tratamientos_id' => $this->tratamientoId,
            'paciente_trat_id' => $paciente_trat->id,
            'paciente_etapas_id' => $etapaId,
        ]);
        $Tmensaje->save();
        // Limpiar el campo de mensaje
        $this->mensajes[$etapaId] = '';
        $this->loadEtapas($this->selectedTratamiento);
        $this->dispatch('mensaje');
        $etapa = Etapa::find($etapaId);
        $trat = Tratamiento::find($this->tratamientoId);

        Mail::to($this->paciente->clinica->email)->send(new NotificacionMensaje($this->paciente, $etapa, $trat, $mensaje));

    }

    // CAMBIO ESTADO PACIENTE ETAPA
    public function estado($pacienteId, $etapaId, $newStatus)
    {
        $tratamientoEtapa = TratamientoEtapa::where('etapa_id', $etapaId)->first();

        if ($tratamientoEtapa) {
            if($newStatus === 'Finalizado'){
                PacienteEtapas::where('paciente_id', $pacienteId)
                ->where('etapa_id', $tratamientoEtapa->etapa_id)
                ->update(['status' => $newStatus, 'fecha_fin' => now()]);
            }else{
                PacienteEtapas::where('paciente_id', $pacienteId)
                ->where('etapa_id', $tratamientoEtapa->etapa_id)
                ->update(['status' => $newStatus]);
            }

            $this->mostrarMenu = false; // Cerrar el menú
            $this->dispatch('estadoActualizado');
            $this->loadEtapas($this->tratamientoId);

            $trat = Tratamiento::find($this->tratamientoId);
            $etapa = Etapa::find($etapaId);

            Mail::to($this->paciente->clinica->email)->send(new CambioEstado($this->paciente, $newStatus, $trat,$etapa));
        }
    }

    // REVISIÓN FECHA
    public function abrirModalRevision($id)
    {
        $this->etapaId = $id;
        $this->modalOpen = true;
    }

    public function revisionEtapa(){

        // Actualizar la revisión en la tabla paciente_etapas
        $etapaPaciente = PacienteEtapas::find($this->etapaId);

        if ($etapaPaciente) {
            $etapaPaciente->revision = $this->revision; // Actualiza el campo 'revision'
            $etapaPaciente->save(); // Guarda los cambios

            $this->dispatch('revision');
            $this->modalOpen = false;
            Mail::to($this->paciente->clinica->email)->send(new NotificacionRevision($this->paciente, $etapaPaciente));

            $this->loadEtapas($this->selectedTratamiento);

        }
    }

    // Nueva etapa
    public function verificarUltimaEtapa()
    {
        // Verificar si la última etapa está finalizada
        $this->ultimaEtapa = PacienteEtapas::where('paciente_id', $this->pacienteId)
                        ->orderBy('created_at', 'desc')
                        ->first();

        $this->mostrarBotonNuevaEtapa = $this->ultimaEtapa && $this->ultimaEtapa->status == "Finalizado";
    }

    public function nuevaEtapa()
    {
        $this->verificarUltimaEtapa();

        // Determinar el número de la nueva etapa
        $j = 1;
        if (preg_match('/Etapa (\d+)/', $this->ultimaEtapa->etapa->name, $matches)) {
            $i = (int)$matches[1];
            $j = $i + 1;
        }

        // Crear la nueva etapa en la tabla de 'etapas'
        $etapa = Etapa::create([
            'name' => "Etapa " . $j,
        ]);

        // Asociar la nueva etapa al tratamiento en la tabla 'tratamiento_etapa'
        if($this->tratId){
            TratamientoEtapa::create([
                'trat_id' => $this->tratId,
                'etapa_id' => $etapa->id
            ]);
        }else{
            TratamientoEtapa::create([
                'trat_id' => $this->tratamientoId,
                'etapa_id' => $etapa->id
            ]);
        }


        // Asociar la nueva etapa al paciente en la tabla 'paciente_etapas'
        PacienteEtapas::create([
            'paciente_id' => $this->pacienteId,
            'etapa_id' => $etapa->id,
            'fecha_ini' => now(),
            'status' => 'Set Up', // Estado inicial de la nueva etapa
        ]);

        // Emitir un evento para actualizar la vista y recargar las etapas
        $this->dispatch('etapa');
        if(!$this->tratId){
            $this->loadEtapas($this->tratamientoId);
        }
    }

    // GESTIÓN NEW TRATAMIENTO
    public function showTratamientosModal()
    {
        $this->showTratamientoModal = true;
        $this->reset(['selectedTratamiento']);
    }

    public function saveTratamiento()
    {
        // 0. Comprobar que el tratamiento esta finalizado => ultima etapa status Finalizado.

        // 1. Crear o relacionar el tratamiento con el paciente
        $pacienteTrat = PacienteTrat::create([
            'paciente_id' => $this->pacienteId,
            'trat_id' => $this->selectedNewTratamiento,
        ]);

        // 2. Obtener las etapas asociadas al tratamiento
        $tratamientoEtapas = TratamientoEtapa::where('trat_id', $this->selectedNewTratamiento)->get();

        // 3. Relacionar cada etapa con el paciente en la tabla 'paciente_etapas'
        foreach ($tratamientoEtapas as $etapa) {
            PacienteEtapas::create([
                'paciente_id' => $this->pacienteId,
                'etapa_id' => $etapa->etapa_id,
                'fecha_ini' => now(), // Ajusta las fechas si es necesario
                'status' => 'Set Up', // Estado inicial de la etapa
            ]);
        }

        // 4. Cerrar el modal para asignar tratamiento
        $this->showTratamientoModal = false;

        // 5. Enviar notificación de éxito
        $this->dispatch('tratamientoAsignado', 'Tratamiento asignado al paciente ' . $this->paciente->name);

        // 6. Actualizar la selección de tratamientos para recargar las etapas del tratamiento recién asignado
        $this->loadEtapas($this->selectedNewTratamiento);
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
                $archivo = Archivos::create([
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
            $this->reset(['selectedTratamiento']);
        }
        $this->loadEtapas($this->selectedTratamiento);
    }

    public function toggleMenu($etapaId)
    {
        $this->mostrarMenu[$etapaId] = isset($this->mostrarMenu[$etapaId]) ? !$this->mostrarMenu[$etapaId] : true;
    }

}
