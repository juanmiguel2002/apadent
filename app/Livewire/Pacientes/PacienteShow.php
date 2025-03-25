<?php

namespace App\Livewire\Pacientes;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Etapa;
use App\Models\Paciente;
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

    public $stripping = [], $verStripping = false;
    public $clinica;

    public $maxFileSize = 15; // Tamaño máximo en MB

    public function mount($id)
    {
        $this->pacienteId = $id;

        // Cargar paciente con sus clínicas y tratamientos, optimizando la carga de datos
        $this->paciente = Paciente::with('clinicas')->findOrFail($this->pacienteId);
        $this->clinica = $this->paciente->clinicas;

        // Obtener tratamientos con sus etapas, mensajes y archivos de manera optimizada
        $this->tratamientos = $this->paciente->tratamientos()->with([
            'etapas' => function ($query) {
                $query->with(['mensajes.user', 'archivos']);
            }
        ])->get();

        // Obtener todas las etapas en una colección para evitar `flatMap`
        $this->etapas = $this->tratamientos->pluck('etapas')->flatten();

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

    public function savePaciente() {
        // Obtener el nombre y apellido del paciente para la nueva carpeta
        $nuevoNombreCarpeta = $this->name . ' ' . $this->apellidos;

        // Primero, actualizamos los datos del paciente en la base de datos
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

        // Ahora, renombramos la carpeta en el disco 'clinicas' si es necesario
        // Suponemos que la carpeta actual del paciente está en 'clinicas/{old_name}'
        $clinicaName = preg_replace('/\s+/', '_', trim($this->clinica->name));
        $pacienteName = preg_replace('/\s+/', '_', trim($this->paciente->name . ' ' . $this->paciente->apellidos));
        $carpetaAntigua = $clinicaName . '/pacientes/' . $pacienteName;
        $carpetaNueva = 'clinicas/' . $nuevoNombreCarpeta;

        // Verificar si la carpeta antigua existe y renombrarla
        if (Storage::exists($carpetaAntigua)) {
            Storage::move($carpetaAntigua, $carpetaNueva);
        }

        // Actualizamos la tabla 'carpetas' con el nuevo nombre
        $carpetaPaciente = Carpeta::where('nombre', $pacienteName)
            ->whereHas('parent', function ($query) {
                $query->where('nombre', 'pacientes');
            })->first();

        if ($carpetaPaciente) {
            $carpetaPaciente->update([
                'nombre' => $nuevoNombreCarpeta, // Actualizar el campo 'nombre' de la carpeta con el nuevo nombre
            ]);
        }

        // Llamar a un evento para indicar que el paciente fue editado
        $this->dispatch('pacienteEdit');

        // Cerrar el modal
        $this->showModalPaciente = false;

        // Recargar la información del paciente (esto probablemente depende de tu implementación)
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
            'stripping.*' => 'required|image|max:15360',
        ],[
            'stripping.*.required' => 'Debe seleccionar al menos un archivo para subir.',
            'stripping.*.image' => 'El archivo debe ser una imagen.',
        ]);
        // Generar nombres para las carpetas
        $clinicaName = preg_replace('/\s+/', '_', trim($this->clinica->name));
        $pacienteName = preg_replace('/\s+/', '_', trim($this->paciente->name . ' ' . $this->paciente->apellidos));
        $pacienteFolder = $clinicaName . '/pacientes/' . $pacienteName;

        $carpetaPaciente = Carpeta::where('nombre', $pacienteName)
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
