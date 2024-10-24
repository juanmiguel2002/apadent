<?php

namespace App\Livewire;

use App\Mail\CambioEstado;
use App\Models\Etapa;
use App\Models\Mensaje;
use App\Models\Paciente;
use App\Models\PacienteEtapas;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;
use App\Models\TratamientoEtapa;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Mail\NotificacionMensaje;
use App\Mail\NotificacionRevision;
use App\Models\Archivos;
use Illuminate\Support\Facades\Auth;

class HistorialPaciente extends Component
{
    use WithFileUploads;

    public $paciente, $pacienteId;
    public $tratamiento, $tratamientos, $tratamientoId;
    public $etapa_paciente = [], $etapas;
    public $mensajes = [], $revision;
    public $selectedTratamiento, $selectedNewTratamiento;
    public $showTratamientoModal = false, $mostrar = false, $modalOpen = false, $documents = false;
    public $etapaId; // para guardar el ID de la etapa correspondiente
    public $archivo, $img = false;
    public $modalImg, $imagenes = [], $modalArchivo = false, $archivos = [];

    public $statuses = [
        'En proceso' => 'bg-green-600',
        'Pausado' => 'bg-blue-600',
        'Finalizado' => 'bg-red-600',
        'Set Up' => 'bg-yellow-600'
    ];

    public function mount($paciente, $tratamiento)
    {
        $this->paciente = $paciente;
        $this->pacienteId = $this->paciente->id;
        $this->tratamiento = $tratamiento; //Saca el tratamiento pasado por el paciente
        $this->tratamientos = Tratamiento::all();   // Carga todos los tratamientos
        $this->archivo = Archivos::where('paciente_id', $this->pacienteId)->where('tipo', 'zip')->get();
    }

    public function updatedSelectedTratamiento($tratamientoId)
    {
        $this->tratamientoId = $tratamientoId;
        if ($this->tratamientoId) {
            // Filtrar por el tratamiento específico y paciente

            $this->etapas = PacienteEtapas::with('etapa','mensajes.user', 'archivos')
                ->where('paciente_id', $this->paciente->id)
                ->whereHas('etapa.tratamientos', function($query) {
                    $query->where('tratamiento_etapa.trat_id', $this->tratamientoId);
                })
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.historial-paciente', [
            'etapas' => $this->etapas,
        ]);
    }

    // COMPRUEBA SI TIENE ARCHIVO UNA ETAPA
    public function tieneArchivos($etapaId)
    {
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
        $this->updatedSelectedTratamiento($this->selectedTratamiento);
        $this->dispatch('mensaje');
        $etapa = Etapa::find($etapaId);
        $trat = Tratamiento::find($this->tratamientoId);

        // Mail::to($this->paciente->clinica->email)->send(new NotificacionMensaje($this->paciente, $etapa, $trat, $mensaje));

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

            $this->mostrar = false; // Cerrar el menú
            $this->dispatch('estadoActualizado');
            $this->updatedSelectedTratamiento($this->tratamientoId);
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

            $this->updatedSelectedTratamiento($this->selectedTratamiento);

        }
    }

    // Nueva etapa
    public function nuevaEtapa()
    {
        // Obtener la última etapa para el paciente y tratamiento dado
        $ultimaEtapa = PacienteEtapas::where('paciente_id', $this->pacienteId)
                // ->where('tratamiento_id', $this->selectedTratamiento) // Ajusta según tu modelo
                ->orderBy('created_at', 'desc')
                ->first();

        // Determinar el número de la nueva etapa
        $j = 1;
        if ($ultimaEtapa && preg_match('/Etapa (\d+)/', $ultimaEtapa->etapa->name, $matches)) {
            $i = (int)$matches[1];
            $j = $i + 1;
        }

        // Crear una nueva etapa
        $etapa = Etapa::create([
            'name' => "Etapa " . $j,
        ]);

        // Asociar la nueva etapa al tratamiento en la tabla tratamiento_etapa
        TratamientoEtapa::create([
            'trat_id' => $this->selectedTratamiento,
            'etapa_id' => $etapa->id
        ]);

        // Asociar la nueva etapa al paciente en la tabla paciente_etapas
        PacienteEtapas::create([
            'paciente_id' => $this->pacienteId,
            'etapa_id' => $etapa->id,
            'fecha_ini' => now(),
            // 'tratamiento_id' => $this->selectedTratamiento,
            'status' => 'Set Up', // O el estado que prefieras
        ]);
        // Emitir un evento para actualizar la vista
        $this->dispatch('etapa');
        $this->updatedSelectedTratamiento($this->selectedTratamiento);
    }

    // GESTIÓN NEW TRATAMIENTO
    public function showTratamientosModal()
    {
        $this->showTratamientoModal = true;
        $this->reset(['selectedTratamiento']);
    }

    public function saveTratamiento()
    {
        // 1. Obtener el paciente
        $paciente = Paciente::findOrFail($this->pacienteId);

        // 2. Crear o relacionar el tratamiento con el paciente
        $pacienteTrat = PacienteTrat::create([
            'paciente_id' => $this->pacienteId,
            'trat_id' => $this->selectedNewTratamiento,
        ]);

        // 3. Obtener las etapas asociadas al tratamiento
        $tratamientoEtapas = TratamientoEtapa::where('trat_id', $this->selectedNewTratamiento)->get();

        // 4. Relacionar cada etapa con el paciente en la tabla 'paciente_etapas'
        foreach ($tratamientoEtapas as $etapa) {
            $pacienteEtapa = PacienteEtapas::create([
                'paciente_id' => $this->pacienteId,
                'etapa_id' => $etapa->etapa_id,
                // 'tratamiento_id' => $this->selectedNewTratamiento,
                'fecha_ini' => now(), // Ajusta las fechas si es necesario
                'status' => 'Set Up', // Estado inicial, por ejemplo
            ]);
            $pacienteEtapa->save();
        }
        $this->showTratamientoModal = false;
        $this->dispatch('tratamientoAsignado','Tratamiento asignado al paciente '. $paciente->name);
    }

    // Nueva Documentación
    public function showDocumentacionModal(){
        $this->documents = true;
    }

    public function saveDocumentacion(){

    }

    // GESTIONAR IMÁGENES ETAPA PACIENTE TRATAMIENTO
    public function verImg($etapaId){
        return redirect()->route('imagenes.ver', ['paciente' => $this->pacienteId, 'etapa' => $etapaId]);
    }

    public function showModalImg(){
        $this->modalImg = true;
    }

    public function saveImg($etapaId){

        $etapa = Etapa::find($etapaId);
        $clinicaName = preg_replace('/\s+/', '_', trim(Auth::user()->clinicas->first()->name));
        $pacienteName = preg_replace('/\s+/', '_', trim($this->paciente->name . ' ' . $this->paciente->apellidos));
        $pacienteFolder = $clinicaName . '/pacientes/' . $pacienteName;

        // Subir múltiples imágenes del paciente, si existen
        if ($this->imagenes && is_array($this->imagenes)) {
            foreach ($this->imagenes as $key => $imagen) {
                $extension = $imagen->getClientOriginalExtension();
                $fileName = $etapa->name."_" . $key . '.' . $extension;
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

        // Subir múltiples imágenes del paciente, si existen
        if ($this->imagenes && is_array($this->imagenes)) {
            foreach ($this->imagenes as $key => $imagen) {
                $extension = $imagen->getClientOriginalExtension();
                $fileName = $etapa->name."_" . $key . '.' . $extension;
                $path = $imagen->storeAs($pacienteFolder . '/archivoEtapa', $fileName, 'clinicas');

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
        $this->updatedSelectedTratamiento($this->selectedTratamiento);
    }

    public function toggleMenu()
    {
        $this->mostrar = !$this->mostrar;
    }

}
