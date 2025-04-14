<?php

namespace App\Livewire\Pacientes;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Etapa;
use App\Models\Paciente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PacienteShow extends Component
{
    use WithFileUploads;

    public $paciente, $pacienteId;
    public $tratamientos, $etapas = [];
    public $showModal = false, $showModalPaciente = false;

    public $num_paciente, $name, $apellidos, $email, $fecha_nacimiento, $telefono;
    public $observacion, $obser_cbct, $odontograma;

    public $stripping = [];
    public $verStripping = false;
    public $clinica;

    public $maxFileSize = 15; // Tamaño máximo en MB

    public function mount($id)
    {
        $this->pacienteId = $id;

        // Cargar paciente con clínicas
        $this->paciente = Paciente::with('clinicas')->findOrFail($this->pacienteId);
        $this->clinica = $this->paciente->clinicas;

        // Obtener tratamientos con etapas que pertenecen al paciente actual
        $this->tratamientos = $this->paciente->tratamientos()
            ->with(['etapas' => function ($query) {
                $query->where('paciente_id', $this->pacienteId)
                    ->with(['mensajes.user', 'archivos']);
            }])
            ->get();

        // Filtrar y obtener solo las etapas relacionadas con el paciente y su tratamiento
        $this->etapas = $this->tratamientos->flatMap(function ($tratamiento) {
            return $tratamiento->etapas->filter(function ($etapa) use ($tratamiento) {
                return $etapa->trat_id === $tratamiento->id && $etapa->paciente_id === $this->pacienteId;
            });
        });

        // Verificar si hay archivos "stripping"
        $this->verStripping = Archivo::where('paciente_id', $this->pacienteId)
            ->where('tipo', 'stripping')
            ->exists();
    }

    public function toggleActivo()
    {
        // Alternar el estado de "activo" del paciente
        $this->paciente->activo = !$this->paciente->activo;
        $this->paciente->save();

        // Despachar evento después de guardar
        $this->dispatch('PacienteActivo', $this->paciente->activo == 1 ? 'Paciente activado' : 'Paciente desactivado');

        // Establecer el nuevo estado basado en el estado del paciente
        $nuevoStatus = $this->paciente->activo ? 'En proceso' : 'Pausado';

        // Actualizar el estado de las etapas relacionadas con el paciente
        Etapa::whereHas('fase.tratamiento.pacientes', function ($query) {
            $query->where('id', $this->pacienteId);
        })
        ->where('status', '!=', 'Finalizado') // Solo actualizamos etapas que no estén "Finalizadas"
        ->update(['status' => $nuevoStatus]);

        // Redirigir al dashboard
        return redirect()->route('dashboard');
    }

    public function fechaEtapa($tratId) {
        $primeraEtapa = Etapa::whereHas('fase', function ($query) use ($tratId) {
            $query->where('trat_id', $tratId);
        })
        ->orderBy('created_at', 'asc') // Ordenar por fecha de creación ascendente
        ->first();
        $fechaPrimeraEtapa = $primeraEtapa ? $primeraEtapa->created_at : null;

       return $fechaPrimeraEtapa;

    }

    public function render()
    {
        return view('livewire.pacientes.paciente-show');
    }

    // Editar Paciente.
    public function edit()
    {
        $this->showModalPaciente = true;
        $this->pacienteId = $this->paciente->id;
        $this->name = $this->paciente->name;
        $this->apellidos = $this->paciente->apellidos;
        $this->email = $this->paciente->email;
        $this->telefono = $this->paciente->telefono;
        $this->fecha_nacimiento = $this->paciente->fecha_nacimiento;
        $this->observacion = $this->paciente->observacion;
        $this->obser_cbct = $this->paciente->obser_cbct;
        $this->odontograma = $this->paciente->odontograma_obser;
    }

    public function savePaciente()
    {
        // Backup del nombre actual de la carpeta
        $nombreAnterior = preg_replace('/\s+/', '_', trim(
            "{$this->paciente->name} " . strtok($this->paciente->apellidos, ' ') . "{$this->paciente->num_paciente}"
        ));
        $nombreAnteriorBBDD = "{$this->paciente->name} " . strtok($this->paciente->apellidos, ' ') . "_{$this->paciente->num_paciente}";

        if(Auth::user()->hasRole('admin')) {
            // Crear nuevo nombre actualizado
            $nuevoNombre = preg_replace('/\s+/', '_', trim(
                "{$this->name} " . strtok($this->apellidos, ' ') . " {$this->paciente->num_paciente}"
            ));
            $nuevoNombreBBDD = "{$this->name} " . strtok($this->apellidos, ' ') . "_{$this->paciente->num_paciente}";

            // Nombre de clínica
            $nombreClinica = preg_replace('/\s+/', '_', trim($this->clinica->name));

            // Rutas físicas
            $rutaAnterior = "{$nombreClinica}/pacientes/{$nombreAnterior}";
            $rutaNueva = "{$nombreClinica}/pacientes/{$nuevoNombre}";

            // Renombrar carpeta en almacenamiento si existe
            if (Storage::disk('clinicas')->exists($rutaAnterior)) {
                Storage::disk('clinicas')->move($rutaAnterior, $rutaNueva);
            }

            // Estructura de carpetas en la base de datos
            $carpetaClinica = Carpeta::firstOrCreate([
                'nombre' => $this->clinica->name,
                'carpeta_id' => null
            ]);

            $carpetaPacientes = Carpeta::firstOrCreate([
                'nombre' => 'pacientes',
                'carpeta_id' => $carpetaClinica->id,
                'clinica_id' => $this->paciente->clinica_id
            ]);

            // Buscar carpeta del paciente con el nombre anterior
            $carpetaPaciente = Carpeta::where([
                'nombre' => $nombreAnteriorBBDD,
                'carpeta_id' => $carpetaPacientes->id,
                'clinica_id' => $this->paciente->clinica_id
            ])->first();

            if ($carpetaPaciente) {
                // Actualizar nombre de la carpeta principal
                $carpetaPaciente->update(['nombre' => $nuevoNombreBBDD]);
            }else {
                return session()->flash('error', 'No se encontró la carpeta del paciente.');
            }

            // Actualizar paciente
            $this->paciente->update([
                'name' => $this->name,
                'apellidos' => $this->apellidos,
                'email' => $this->email,
                'fecha_nacimiento' => $this->fecha_nacimiento,
                'telefono' => $this->telefono,
                'observacion' => $this->observacion,
                'obser_cbct' => $this->obser_cbct,
                'odontograma_obser' => $this->odontograma,
            ]);
        }
        // Emitir evento, cerrar modal y recargar
        $this->dispatch('pacienteEdit');
        $this->showModalPaciente = false;
        $this->mount($this->pacienteId);

    }

    // GESTIÓN DE Stripping
    public function showStripping()
    {
        $this->showModal = true;
    }

    public function saveStripping()
    {
        $this->validate([
            'stripping.*' => 'required|file|max:15360',
        ],[
            'stripping.*.required' => 'Debe seleccionar al menos un archivo para subir.',
            'stripping.*.file' => 'El archivo debe ser una imagen.',
        ]);
        // Generar nombres para las carpetas
        $clinicaName = preg_replace('/\s+/', '_', trim($this->clinica->name));
        $primerApellido = strtok($this->paciente->apellidos, " ");

        $nombreP = preg_replace('/\s+/', '_', trim($this->paciente->name . ' ' . $primerApellido . ' ' . $this->paciente->num_paciente));
        $nombrePaciente = $this->paciente->name . ' ' . $primerApellido . '_' . $this->paciente->num_paciente;

        $pacienteFolder = $clinicaName . '/pacientes/' . $nombreP;

        $carpetaPaciente = Carpeta::where('nombre', $nombrePaciente)
            ->whereHas('parent', function ($query) {
                $query->where('nombre', 'pacientes');
            })->first();

        // Verificar si la carpeta de Stripping ya existe
        $carpeta = Carpeta::where('nombre', 'Stripping')
            ->where('carpeta_id', $carpetaPaciente->id)
            ->first();

        // Subir múltiples imágenes del paciente, si existen
        if ($this->stripping != null) {
            foreach ($this->stripping as $key => $imagen) {
                $extension = $imagen->getClientOriginalExtension();
                $fileName = "Stripping_". $this->paciente->name."_" . $key . '.' . $extension; //nombre del archivo
                $path = $imagen->storeAs($pacienteFolder . '/Stripping', $fileName, 'clinicas');

                Archivo::create([
                    'name' => pathinfo($fileName, PATHINFO_FILENAME),
                    'ruta' => $path,
                    'tipo' => 'stripping',
                    'extension' => $extension,
                    'carpeta_id' => $carpeta->id,
                    'paciente_id' => $this->paciente->id,
                ]);
            }
            $this->verStripping = true;
        }else{
            return session()->flash('error', 'No se han seleccionado imágenes.');
        }

        $this->dispatch('stripping');
        $this->showModal = false;
        $this->mount($this->pacienteId);
    }

    public function close()
    {
        if($this->showModalPaciente){
            $this->showModalPaciente = false;
        }else{
            $this->showModal = false;
        }
        $this->mount($this->pacienteId);
    }
}
