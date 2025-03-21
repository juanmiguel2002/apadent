<?php

namespace App\Livewire\Pacientes;

use App\Jobs\EnviarRecordatorioRevision;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Mail\CambioEstado;
use App\Mail\NotificacionMensaje;
use App\Models\Clinica;
use App\Models\Etapa;
use App\Models\Mensaje;
use App\Models\Archivo;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;
use App\Mail\NotificacionRevision;
use App\Models\Carpeta;
use App\Models\Fase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HistorialPaciente extends Component
{
    use WithFileUploads;

    public $clinica, $paciente, $pacienteId;
    public $tratamientoPaciente, $tratamiento, $tratamientos, $tratamientoId;
    public $etapas, $mensajes = [], $revision;
    public $selectedTratamiento, $selectedNewTratamiento, $tratId;
    public $showTratamientoModal = false, $mostrarMenu = [], $modalOpen = false;
    public $etapaId; // para guardar el ID de la etapa correspondiente
    public $archivo, $img = false, $tipo;
    public $modalImg, $imagenes = [];
    // public $modalArchivo = false, $archivos = [];

    // nueva documentación
    public $documents = false, $documentos = false;
    public $documentacion = [], $selectedEtapa, $mensaje;
    public $pacienteFolder, $pacienteName, $clinicaName;

    public $maxFileSize = 15; // Tamaño máximo de imágenes en MB
    public $maxFile = 4; // Tamaño máximo de archivo en GB

    public $statuses = [
        'En proceso' => 'bg-green-600',
        'Pausado' => 'bg-blue-600',
        'Finalizado' => 'bg-red-600',
        'Set Up' => 'bg-yellow-600'
    ];

    public function mount($paciente, $tratId = null)
    {
        $this->paciente = $paciente;
        $this->clinica = Clinica::find($this->paciente->clinica_id);
        $this->pacienteId = $this->paciente->id;
        $this->tratamientoId = session('tratamientoId', ''); // se recupera si existe, si no se carga en vacio

        $this->tratId = $tratId;// comprobar si existe tratamiento asignado por url

        // Cargar las fases del tratamiento seleccionado (si existe un tratamiento)
        if ($this->tratId) {
            $this->loadEtapas($this->tratId);
        }else{
            $this->loadEtapas($this->tratamientoId);
        }
        // $this->documentos = Archivo::where('paciente_id', $this->pacienteId)
        //     ->where('tipo', 'archivoscomplementarios') // Asegurar que la comparación es insensible a mayúsculas
        //     ->get();
        $this->documentos = $this->tieneDocumentos();


        // Cargar archivos relacionados con las etapas del paciente
        $this->archivo = Archivo::whereHas('etapas', function ($query) {
            $query->where('paciente_id', $this->pacienteId);
        })->where('extension', 'zip')->get();

        // Archivos
        $clinicaName = preg_replace('/\s+/', '_', trim($this->clinica->name));
        $primerApellido = strtok($paciente->apellidos, " ");
        $this->pacienteName = preg_replace('/\s+/', '_', trim($this->paciente->name . ' ' . $primerApellido . ' ' . $this->paciente->num_paciente));
        $this->pacienteFolder = "{$clinicaName}/pacientes/{$this->pacienteName}";

    }

    public function updatedTratamientoId($value)
    {
        // Guardar en sesión cada vez que se actualiza
        session(['tratamientoId' => $value]);
    }

    public function loadEtapas($tratamientoId)
    {
        // Cargar las etapas específicas para la fase activa
        $this->etapas = Etapa::whereHas('tratamiento', function ($query) use ($tratamientoId) {
            $query->where('id', $tratamientoId);
        })
        ->where('paciente_id', $this->pacienteId)
        ->with('fase')
        ->get();
        session(['tratamientoId' => $tratamientoId]);
    }

    public function render()
    {
        return view('livewire.pacientes.historial-paciente');
    }

    // COMPRUEBA SI TIENE ARCHIVO UNA ETAPA
    public function tieneArchivos($etapaId, $archivo = false, $tipo)
    {
        if($archivo){
           return Archivo::where('etapa_id', $etapaId)->where('extension', 'zip')->exists();
        }

        return Archivo::where('etapa_id', $etapaId)
                      ->where('tipo', $tipo)
                      ->exists();
    }

    public function tieneDocumentos()
    {
        // Verificar si hay un tratamiento válido
        $tratamientoId = $this->tratamientoId ?: $this->tratId;

        // Obtener los IDs de las etapas del tratamiento para este paciente
        $etapas = Etapa::where('trat_id', $tratamientoId)
                    ->where('paciente_id', $this->pacienteId)
                    ->pluck('id');

        // Si no hay etapas, no hay documentos
        if ($etapas->isEmpty()) {
            return false;
        }

        // Comprobar si existen archivos en alguna de esas etapas
        return Archivo::whereIn('etapa_id', $etapas)
            ->whereRaw('LOWER(tipo) = ?', ['archivoscomplementarios']) // Asegurar que la comparación es insensible a mayúsculas
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
            $etapa->save();
        }

        $this->mostrarMenu = false; // Cerrar el menú
        $this->loadEtapas($this->tratId ? $this->tratId : $this->tratamientoId);
        $this->dispatch('estadoActualizado');

        // Enviar email a la clínica
        $etapa = Etapa::find($etapaId);
        $trat = Tratamiento::find($this->tratId ? $this->tratId : $this->tratamientoId);

        if ($this->clinica && $this->clinica->email) {
            Mail::to($this->clinica->email)->send(new CambioEstado($this->paciente, $newStatus, $etapa, $trat, $this->clinica));
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
            $this->loadEtapas($this->tratId ? $this->tratId : $this->selectedTratamiento);

            Mail::to($this->clinica->email)->send(new NotificacionRevision($this->paciente, $etapa, $this->clinica));
            EnviarRecordatorioRevision::dispatch($this->paciente->id);
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
        $this->mount($this->paciente, $this->tratId ? $this->tratId : null);
        // Mail clinica

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

        // Verificar si el paciente ya tiene asignado este tratamiento
        $existe = PacienteTrat::where('paciente_id', $this->pacienteId)
            ->where('trat_id', $this->selectedNewTratamiento)
            ->exists();

        if ($existe) {
            return redirect()->back()->with('error', 'Este tratamiento ya está asignado a este paciente.');
        }

        // try {
            DB::transaction(function () {
                // Obtener el tratamiento seleccionado
                $tratamiento = Tratamiento::findOrFail($this->selectedNewTratamiento);
                $tratName = preg_replace('/\s+/', '_', trim($tratamiento->name . ' ' . $tratamiento->descripcion));
                $tratCarpeta = "{$this->pacienteFolder}/{$tratName}";

                // Asociar el tratamiento al paciente
                PacienteTrat::create([
                    'paciente_id' => $this->pacienteId,
                    'trat_id' => $this->selectedNewTratamiento,
                ]);

                // Buscar la carpeta del paciente dentro de la clínica
                $carpetaPaciente = Carpeta::where('nombre', $this->pacienteName)
                    ->whereHas('parent', fn($query) => $query->where('nombre', 'pacientes'))
                    ->first();

                if (!$carpetaPaciente) {
                    throw new \Exception('Carpeta del paciente no encontrada.');
                }

                // Crear la carpeta del tratamiento dentro del paciente
                $carpetaTratamiento = Carpeta::firstOrCreate([
                    'nombre'      => $tratName,
                    'carpeta_id'  => $carpetaPaciente->id,
                    'clinica_id' => $this->clinica->id
                ]);

                // Crear carpeta en sistema de archivos si no existe
                if (!Storage::disk('clinicas')->exists($tratCarpeta)) {
                    Storage::disk('clinicas')->makeDirectory($tratCarpeta);
                }

                $subFolders = ['imgEtapa', 'CBCT', 'archivoComplementarios', 'Rayos', 'Stripping'];
                foreach ($subFolders as $subFolder) {
                    $subFolderPath = "{$tratCarpeta}/{$subFolder}";
                    if (!Storage::disk('clinicas')->exists($subFolderPath)) {
                        Storage::disk('clinicas')->makeDirectory($subFolderPath);
                    }
                    Carpeta::firstOrCreate([
                        'nombre' => $subFolder,
                        'carpeta_id' => $carpetaTratamiento->id,
                        'clinica_id' => $this->clinica->id
                    ]);
                }
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
                        'trat_id' => $this->selectedNewTratamiento,
                    ]);
                }
            });

            // Resetear el tratamiento seleccionado y cerrar el modal
            $this->reset('selectedNewTratamiento');
            $this->closeModal();

            // Emitir un evento para actualizar la lista de tratamientos en la vista
            $this->dispatch('tratamientoAsignado', 'Tratamiento asignado exitosamente.');
            $this->dispatch('recargar-pagina');

        // } catch (\Exception $e) {
        //     return redirect()->back()->with('error', 'Ocurrió un error al asignar el tratamiento.');
        // }
    }

    // Nueva Documentación
    public function showDocumentacionModal()
    {
        $this->documents = true;
        $this->loadEtapas($this->tratId ? $this->tratId : $this->tratamientoId);
        if ($this->tratamientoId) {
            $this->tratamiento = Tratamiento::find($this->tratamientoId);
        }
    }

    public function updatedSelectedtratamientoId($tratamientoId)
    {
        $this->tratamientoId = $tratamientoId;
        $this->loadEtapas($this->tratamientoId);
    }

    public function saveDocumentacion() {

        $etapa = Etapa::findOrFail($this->selectedEtapa);
        $tratamiento = Tratamiento::findOrFail($etapa->trat_id);
        $tratName = preg_replace('/\s+/', '_', trim($tratamiento->name .' '. $tratamiento->descripcion));

        // Buscar si ya existen archivos subidos en esta etapa
        $existeArchivo = Archivo::where('etapa_id', $etapa->id)
            ->where('tipo', 'archivocomplementarios')
            ->exists();

        if ($existeArchivo) {
            return session()->flash('error', 'Ya existen archivos en esta etapa. No se pueden subir más.');
        }

        // Buscar la carpeta del paciente dentro de la clínica
        $carpetaPaciente = Carpeta::where('nombre', $this->pacienteName)
            ->whereHas('parent', fn($query) => $query->where('nombre', 'pacientes'))
            ->first();

        if (!$carpetaPaciente) {
            return session()->flash('error', 'Carpeta del paciente no encontrada.');
        }

        // Buscar o crear la carpeta del tratamiento dentro del paciente
        $carpetaTratamiento = Carpeta::firstOrCreate([
            'nombre'      => $tratName,
            'carpeta_id'  => $carpetaPaciente->id
        ]);

        // Buscar o crear la carpeta "archivoComplementarios" dentro del tratamiento
        $carpeta = Carpeta::firstOrCreate([
            'nombre'      => 'archivoComplementarios',
            'carpeta_id'  => $carpetaTratamiento->id
        ]);

        // Subir imágenes y guardarlas en la base de datos
        if ($this->documentacion && is_array($this->documentacion)) {
            // Obtener el número más alto de clave (key) en la BD para esta etapa
            $maxKey = Archivo::where('etapa_id', $etapa->id)
                ->where('tipo', 'archivocomplementarios')
                ->where('ruta', 'LIKE', "{$this->pacienteFolder}/{$tratName}/archivoComplementarios/%")
                ->get()
                ->map(function ($archivo) {
                    preg_match('/_archivoComplementarios_(\d+)\./', $archivo->ruta, $matches);
                    return isset($matches[1]) ? (int) $matches[1] : 0;
                })
                ->max();

            foreach ($this->documentacion as $imagen) {
                $extension = $imagen->getClientOriginalExtension();
                $maxKey++; // Aumentamos el índice para evitar duplicados
                $fileName = Str::slug($etapa->name) . "_archivoComplementarios_{$maxKey}.{$extension}";
                $filePath = "{$this->pacienteFolder}/{$tratName}/archivoComplementarios/{$fileName}";

                // Comprobar si el archivo ya existe
                $archivoExistente = Archivo::where('ruta', $filePath)
                    ->where('etapa_id', $etapa->id)
                    ->exists();

                if (!$archivoExistente) {
                    // Guardar archivo en almacenamiento
                    Storage::disk('clinicas')->putFileAs("{$this->pacienteFolder}/{$tratName}/archivoComplementarios", $imagen, $fileName);

                    // Crear registro en la base de datos
                    Archivo::create([
                        'name'       => pathinfo($fileName, PATHINFO_FILENAME),
                        'ruta'       => $filePath,
                        'tipo'       => 'archivocomplementarios',
                        'extension'  => $extension,
                        'etapa_id'   => $etapa->id,
                        'carpeta_id' => $carpeta->id,
                        'paciente_id' => $this->paciente->id,
                    ]);

                    if (file_exists($imagen->getPathname())) {
                        unlink($imagen->getPathname());
                    }
                }
            }

            // Crear mensaje solo si hay contenido
            if ($this->mensaje) {
                Mensaje::create([
                    'user_id' => auth()->id(),
                    'mensaje' => $this->mensaje,
                    'etapa_id' => $etapa->id,
                ]);
            }
        }

        $this->documents = false;
        $this->documentos = true;
        $this->dispatch('archivoComple', 'Archivos complementarios añadidos');
        return redirect()->route('imagenes.ver', [
            'paciente' => $this->pacienteId,
            'etapa' => $etapa->id,
            'tipo' => $this->tipo
        ]);
    }

    // GESTIONAR IMÁGENES ETAPA PACIENTE TRATAMIENTO
    public function showModalImg($etapaId, $tipo){
        $this->etapaId = $etapaId;
        $this->tipo = $tipo;
        $this->modalImg = true;
    }

    public function saveImg()
    {
        $this->validate([
            'imagenes.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:15360', // 15MB
        ], [
            'imagenes.*.image' => 'El archivo debe ser una imagen',
            'imagenes.*.mimes' => 'Formato inválido (jpeg, png, jpg, gif, svg)',
            'imagenes.*.max' => 'El archivo debe pesar menos de 15MB'
        ]);

        $etapa = Etapa::findOrFail($this->etapaId);
        $tratamiento = Tratamiento::findOrFail($etapa->trat_id);
        $tratName = preg_replace('/\s+/', '_', trim($tratamiento->name . ' ' . $tratamiento->descripcion));

        // Buscar la carpeta del paciente dentro de la clínica
        $carpetaPaciente = Carpeta::where('nombre', $this->pacienteName)
            ->whereHas('parent', fn($query) => $query->where('nombre', 'pacientes'))
            ->first();

        if (!$carpetaPaciente) {
            return session()->flash('error', 'Carpeta del paciente no encontrada.');
        }

        // Buscar o crear la carpeta del tratamiento dentro del paciente
        $carpetaTratamiento = Carpeta::firstOrCreate([
            'nombre'      => $tratName,
            'carpeta_id'  => $carpetaPaciente->id
        ]);

        // Determinar la carpeta según el tipo de imagen
        $tipoCarpeta = $this->tipo === 'rayos' ? 'Rayos' : 'imgEtapa';

        // Buscar o crear la carpeta de destino
        $carpeta = Carpeta::firstOrCreate([
            'nombre'      => $tipoCarpeta,
            'carpeta_id'  => $carpetaTratamiento->id
        ]);

        // if (!$this->imagenes || !is_array($this->imagenes)) {
        //     return session()->flash('error', 'No se han seleccionado imágenes válidas.');
        // }

        // Subir imágenes y guardarlas en la base de datos
        foreach ($this->imagenes as $key => $imagen) {
            $extension = $imagen->getClientOriginalExtension();
            $fileName = Str::slug($etapa->name) . "_{$key}_{$tipoCarpeta}.{$extension}";
            $fileName = preg_replace('/[^\w.-]/', '_', $fileName); // Limpieza del nombre

            $filePath = "{$this->pacienteFolder}/{$tratName}/{$tipoCarpeta}/{$fileName}";

            // Subir el archivo
            if (Storage::disk('clinicas')->putFileAs("{$this->pacienteFolder}/{$tratName}/{$tipoCarpeta}", $imagen, $fileName)) {
                // Guardar en la base de datos solo si se subió correctamente
                Archivo::create([
                    'name'       => pathinfo($fileName, PATHINFO_FILENAME),
                    'ruta'       => $filePath,
                    'tipo'       => strtolower($tipoCarpeta),
                    'extension'  => $extension,
                    'etapa_id'   => $this->etapaId,
                    'carpeta_id' => $carpeta->id,
                    'paciente_id' => $this->paciente->id,
                ]);

                if (file_exists($imagen->getPathname())) {
                    unlink($imagen->getPathname());
                }
            }
        }

        $this->modalImg = false;
        $this->dispatch('imagen');

        return redirect()->route('imagenes.ver', [
            'paciente' => $this->pacienteId,
            'etapa' => $this->etapaId,
            'tipo' => $this->tipo
        ]);
    }

    public function closeModal(){
        if($this->documents) {
            $this->documents = false;
            $this->reset(['documentacion', 'mensaje', 'selectedEtapa']);
        }elseif ($this->modalOpen){
            $this->modalOpen = false;
            $this->reset(['revision']);
        }elseif($this->modalImg){
            $this->modalImg = false;
        }else {
            $this->showTratamientoModal = false;
            // $this->reset(['selectedNewTratamiento']);
        }
        $this->loadEtapas($this->tratId ? $this->tratId : $this->tratamientoId);
    }

    public function toggleMenu($etapaId)
    {
        $this->mostrarMenu[$etapaId] = isset($this->mostrarMenu[$etapaId]) ? !$this->mostrarMenu[$etapaId] : true;
    }
}
