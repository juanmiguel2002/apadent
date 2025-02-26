<?php

namespace App\Livewire\Pacientes;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Etapa;
use App\Models\Paciente;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PacienteShow extends Component
{
    use WithFileUploads, WithPagination;

    public $paciente, $tratamientos, $pacienteId;
    public $showModal = false, $showModalPaciente = false;
    public $num_paciente, $name, $apellidos, $email, $fecha_nacimiento, $telefono;
    public $observacion, $obser_cbct, $odontograma;
    public $fases, $etapas, $stripping = [], $verStripping = false;
    public $clinica;

    public function mount($id)
    {
        $this->pacienteId = $id;

        // Cargar paciente con sus clínicas y tratamientos, incluyendo sus etapas con mensajes y archivos
        $this->paciente = Paciente::with([
            'clinicas',
            'tratamientos.etapas' => function ($query) {
                $query->where('paciente_id', $this->pacienteId) // Filtrar por paciente
                    ->with(['mensajes.user', 'archivos']);
            }
        ])->findOrFail($this->pacienteId);

        $this->clinica = $this->paciente->clinicas->first();

        // Obtener tratamientos del paciente
        $this->tratamientos = $this->paciente->tratamientos;

        // Obtener etapas directamente de los tratamientos ya cargados (evitando una consulta extra)
        $this->etapas = $this->tratamientos->flatMap->etapas;
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

    public function historial($id, $tratId){
        return redirect()->route('paciente-historial', ['id' => $id, 'tratId' => $tratId]);
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
        $this->dispatch('pacienteEdit');
        $this->showModalPaciente = false;
    }

    // GESTIÓN DE Stripping
    public function showStripping()
    {
        $this->showModal = true;
    }

    public function saveStripping()
    {

        // $this->validate([
        //     'stripping.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
        // ], [
        //     'stripping.image' => 'Solo se admiten imágenes',
        //     'stripping.*.mimes' => 'Formato de imagen válido: jpeg, png, jpg, gif, svg',
        // ]);

        // Generar nombres para las carpetas
        $clinicaName = preg_replace('/\s+/', '_', trim($this->clinica->name));
        $pacienteName = preg_replace('/\s+/', '_', trim($this->paciente->name . ' ' . $this->paciente->apellidos));
        $pacienteFolder = $clinicaName . '/pacientes/' . $pacienteName;

        $carpetaPaciente = Carpeta::where('nombre', preg_replace('/\s+/', '_', trim($this->paciente->name . ' ' . $this->paciente->apellidos)))
            ->whereHas('parent', function ($query) {
                $query->where('nombre', 'pacientes');
            })->first();
        $carpeta = Carpeta::where('nombre', 'Stripping')
            ->where('carpeta_id', $carpetaPaciente->id)
            ->first();

        // Subir múltiples imágenes del paciente, si existen
        if ($this->stripping && is_array($this->stripping)) {
            foreach ($this->stripping as $key => $imagen) {
                $extension = $imagen->getClientOriginalExtension();
                $fileName = "Stripping_" . $key . '.' . $extension; //nombre del archivo
                $path = $imagen->storeAs($pacienteFolder . '/Stripping', $fileName, 'clinicas');

                // Extraer el nombre del archivo sin la extensión
                $name = pathinfo($fileName, PATHINFO_FILENAME);

                $archivo = Archivo::create([
                    'name' => $name,
                    'ruta' => $path,
                    'tipo' => 'stripping',
                    'extension' => $extension,
                    'carpeta_id' => $carpeta->id,
                ]);
                $this->verStripping = $archivo->where('tipo','stripping')->exist();
            }

        }

        $this->dispatch('stripping');
        $this->showModal = false;
    }

    public function close()
    {
        if($this->showModalPaciente){
            $this->showModalPaciente = false;
        }else{
            $this->showModal = false;
        }
    }
}
