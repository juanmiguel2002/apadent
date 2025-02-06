<?php

namespace App\Livewire\Pacientes;

use App\Models\Etapa;
use App\Models\Fase;
use App\Models\Paciente;
use Livewire\Component;
use Livewire\WithFileUploads;

class PacienteShow extends Component
{
    use WithFileUploads;

    public $paciente, $tratamientosAll, $tratamientos, $pacienteId, $imageUrl;
    public $showModal = false, $showModalPaciente = false;
    public $isCreatingNew = false;
    public $num_paciente, $name, $apellidos, $email, $fecha_nacimiento, $telefono;
    public $observacion, $obser_cbct, $odontograma, $url_img;
    public $fases, $etapas;

    public function mount($id)
    {
        $this->pacienteId = $id;

        // Cargar el paciente con las relaciones necesarias
        $this->paciente = Paciente::with([
            'clinicas',
            'tratamientos'
        ])->findOrFail($this->pacienteId);

        // Cargar los tratamientos del paciente
        $this->tratamientos = $this->paciente->tratamientos;

        if ($this->tratamientos->isNotEmpty()) {
            // Filtrar las fases y etapas por el paciente actual
            $this->fases = Fase::whereHas('etapas', function ($query) {
                $query->where('paciente_id', $this->pacienteId); // Filtrar etapas del paciente
            })
            ->with([
                'etapas' => function ($query) {
                    $query->where('paciente_id', $this->pacienteId) // Filtrar etapas del paciente
                        ->with(['mensajes.user', 'archivos']); // Cargar relaciones necesarias
                }
            ])
            ->get();
            // dd($this->paciente->clinicas);
            // Consolidar las etapas de las fases
            $this->etapas = $this->fases->flatMap->etapas;
        } else {
            // Si no hay tratamientos, inicializa las variables
            $this->fases = collect();
            $this->etapas = collect();
        }
    }
    public function toggleActivo()
    {
        // Alternar el estado de "activo" del paciente
        $this->paciente->activo = !$this->paciente->activo;
        $this->paciente->save();

        // Despachar evento después de guardar
        $this->dispatch('PacienteActivo');

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

    public function render()
    {
        return view('livewire.pacientes.paciente-show');
    }

    public function historial($id, $tratId){
        return redirect()->route('paciente-historial', ['id' => $id, 'tratId' => $tratId]);
    }
    public function verImg($etapaId){
        return redirect()->route('imagenes.ver', ['paciente' => $this->pacienteId, 'etapa' => $etapaId]);
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
        // $this->loadTratamientos();

    }

    // GESTIÓN DE IMAGENES Y ARCHIVOS
    // private function getPacienteImageUrl($paciente)
    // {
    //     if ($paciente->url_img && Storage::disk('clinicas')->exists($paciente->url_img)) {
    //         return route('download.paciente.file', [
    //             'clinica' => preg_replace('/\s+/', '_', trim(Auth::user()->clinicas->first()->name)),
    //             'paciente' => preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $paciente->apellidos)),
    //             'folder' => 'fotoPaciente',
    //             'filename' => basename($paciente->url_img)
    //         ]);
    //     }
    //     return null; // Si no hay imagen, retorna null
    // }
    // public function showImagenes()
    // {
    //     $this->uploadType = 'imagenes';
    //     $this->showModal = true;
    // }

    // public function showCbct()
    // {
    //     $this->uploadType = 'cbct';
    //     $this->showModal = true;
    // }

    // public function save()
    // {
    //     // Validar los archivos subidos
    //     $this->validate([
    //         'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,zip,rar',
    //     ]);

    //     // Obtener el primer tratamiento asociado con el paciente
    //     $etapa = Etapa::where('trat_id', $this->selectedTratamiento)->first();
    //     $this->paciente->tratamientos()
    //         ->where('trat_id', $this->selectedTratamiento)
    //         ->first();

    //     // if (!$etapa) {
    //     //     $this->dispatch('Errordb');
    //     //     return;
    //     // }

    //     $EtapaId = $etapa->id; // Obtener el ID del pivote

    //     foreach ($this->files as $file) {
    //         // Definir la carpeta de almacenamiento basada en el tipo de archivo
    //         $folder = $file->getClientOriginalExtension() == 'zip' ? 'pacienteCbct' : 'imagenPaciente';

    //         // Guardar archivo en la carpeta correspondiente
    //         $path = $file->store($folder, 'public');

    //         // Crear registro en la base de datos
    //         if ($file->getClientOriginalExtension() == 'zip') {
    //             Archivos::create([
    //                 'etapa_id' => $EtapaId,
    //                 'ruta' => $path,
    //             ]);
    //             $this->dispatch('archivo');
    //         } else {
    //             Imagen::create([
    //                 'etapa_id' => $EtapaId,
    //                 'ruta' => $path,
    //             ]);
    //             $this->dispatch('imagne');
    //         }
    //     }

    //     // Mostrar mensaje de éxito y resetear el estado
    //     // $this->dispatch('archivo');
    //     $this->files = [];
    //     $this->close();
    // }

    public function close()
    {
        if($this->showModalPaciente){
            $this->showModalPaciente = false;
        }else{
            $this->showModal = false;
        }
    }
}
