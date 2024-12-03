<?php

namespace App\Livewire;

use App\Models\Etapa;
use App\Models\Fase;
use App\Models\Paciente;
use App\Models\Tratamiento;
use Livewire\Component;
use Livewire\WithFileUploads;

class PacienteShow extends Component
{
    use WithFileUploads;

    public $paciente, $tratamientosAll, $tratamientos, $pacienteId, $imageUrl;
    public $showModal = false, $showModalPaciente = false;
    public $files = [];
    public $uploadType = 'imagenes'; // Default to 'imagenes'
    public $isCreatingNew = false;
    public $num_paciente, $name, $apellidos, $email, $fecha_nacimiento, $telefono;
    public $observacion, $obser_cbct, $odontograma, $url_img;
    public $fases, $etapas;

    public function mount($id)
    {
        $this->pacienteId = $id;

        // Carga el paciente y sus relaciones con tratamientos, fases y etapas
        $this->paciente = Paciente::with(['clinicas', 'tratamientos.fases.etapas'])
            ->findOrFail($this->pacienteId);

        // Cargar tratamientos relacionados
        $this->tratamientos = $this->paciente->tratamientos;

        // Cargar solo las fases que tienen etapas asignadas
        $this->fases = Fase::where('trat_id', $this->tratamientos[0]->id)->whereHas('etapas')->get();
        $this->etapas = Etapa::with(['fase', 'mensajes.user'])
        ->whereHas('fase.tratamiento', function ($query) {
            $query->where('trat_id', $this->tratamientos);
        })
        ->get();
        // dd($this->paciente, $this->tratamientos, $this->fases, $this->etapas);
    }


    public function toggleActivo()
    {
        $this->paciente->activo = !$this->paciente->activo;
        $this->paciente->save();
        $this->dispatch('PacienteActivo');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.paciente-show');
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
        $this->loadTratamientos();

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
