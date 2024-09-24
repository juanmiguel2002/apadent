<?php

namespace App\Livewire;

use App\Models\Paciente;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
    public $num_paciente, $name, $email, $fecha_nacimiento, $telefono;
    public $observacion, $obser_cbct, $url_img;

    protected $rules = [
        'newTratamiento' => 'required|string|max:255',
    ];

    public function mount($id)
    {
        $this->pacienteId = $id;
        $this->paciente = Paciente::findOrFail($id);
        // $this->tratamientosAll = Tratamiento::all();
        $this->loadTratamientos();
        // dd($this->paciente->tratamientos);
    }

    protected function loadTratamientos()
    {
        $this->tratamientos = PacienteTrat::with('tratamiento')
        ->where('paciente_id', $this->paciente->id)
        ->orderBy('created_at', 'desc')
        ->get();    
        // dd($this->tratamientos);
    }
    public function toggleActivo()
    {
        $this->paciente->activo = !$this->paciente->activo;
        $this->paciente->save();
        $this->dispatch('PacienteActivo');
    }

    public function render()
    {
        return view('livewire.paciente-show'
        );
    }

    // Editar Paciente.
    public function edit()
    {
        $this->showModalPaciente = true;
        $this->pacienteId = $this->paciente->id;
        $this->name = $this->paciente->name;
        $this->email = $this->paciente->email;
        $this->telefono = $this->paciente->telefono;
        $this->fecha_nacimiento = $this->paciente->fecha_nacimiento;
        $this->observacion = $this->paciente->observacion;
        $this->obser_cbct = $this->paciente->obser_cbct;
    }

    public function savePaciente() {
        if ($this->url_img) {
            // Definimos la ruta base de la clínica
            $clinicaName = preg_replace('/\s+/', '_', trim(Auth::user()->clinicas->first()->name));
            $pacienteName = preg_replace('/\s+/', '_', trim($this->name));
            $pacienteFolder = 'clinicas/' . $clinicaName . '/pacientes/' . $pacienteName . '/fotoPaciente';

            // Verificamos si la carpeta del paciente no existe y la creamos junto con las subcarpetas necesarias
            if (!Storage::exists($pacienteFolder)) {
                Storage::makeDirectory($pacienteFolder); // Crea la carpeta del paciente y las subcarpetas necesarias
            }

            // Guardamos la imagen dentro de la carpeta del paciente
            $filename = $this->img_paciente->store(
                $pacienteFolder, // Ruta de la clínica y el paciente
                'clinicas' // Guardar en el almacenamiento público
            );
        }

        $this->paciente->update([
            'name' => $this->name,
            'email' => $this->email,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'telefono' => $this->telefono,
            'observacion' => $this->observacion,
            'obser_cbct' => $this->obser_cbct,
            'url_img' => $filename,
        ]);
        $this->dispatch('pacienteEdit');
        $this->showModalPaciente = false;
        $this->resetPage();
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
