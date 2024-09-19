<?php

namespace App\Livewire\Admin;

use App\Mail\CredencialesClinica;
use App\Models\Clinica;
use App\Models\ClinicaUser;
use App\Models\User;
use DragonCode\Support\Facades\Filesystem\File;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Clinicas extends Component
{
    use WithPagination;
    public $showModal, $editable = false;
    public $clinicas, $clinica_id;
    public $ordenar = '';
    public $search = "";
    public $name, $direccion, $telefono, $email, $cif, $direccion_fac, $cuenta;

    protected $listeners =['deleteClinicConfirmed', 'clinicaId'];
    protected $queryString = [
        'search' => ['except' => ''],
        'ordenar' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingOrdenar()
    {
        $this->resetPage();
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'direccion' => 'required|string|max:255',
        'telefono' => 'required',
        'email' => 'required|email|max:255',
        'cif' => 'required|string|max:9',
        'direccion_fac' => 'required|string|max:255',
        'cuenta' => 'required|string|max:255',
    ];

    public function mount() {
        // Definir la columna y dirección de ordenación predeterminadas
        $orderByColumn = 'name';
        $orderByDirection = 'asc';

        // Determinar la columna de ordenación basada en la selección del usuario
        switch ($this->ordenar) {
            case 'recientes':
                $orderByColumn = 'created_at'; // Suponiendo que estás utilizando 'created_at' para ordenar por los más recientes
                $orderByDirection = 'desc';
                break;
            case 'name':
                $orderByColumn = 'name';
                $orderByDirection = 'asc';
                break;
            case 'id':
                $orderByColumn = 'id'; // Asegúrate de que 'codigo' es un campo en tu base de datos
                $orderByDirection = 'asc';
                break;
            default:
                $orderByColumn = 'name';
                $orderByDirection = 'asc';
                break;
        }

        // Realizar la consulta con los filtros y la ordenación seleccionada
        $this->clinicas = Clinica::with('users')
            ->where('name', 'like', '%' . $this->search . '%') // Filtrado por nombre
            ->orderBy($orderByColumn, $orderByDirection) // Ordenar por la columna y dirección seleccionadas
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.clinicas');
    }

    public function showCreateModal($id = null)
    {
        $this->clinica_id = $id;
        if($id){
            $this->editable = true;
            $clinica = Clinica::find($id);
            $this->name = $clinica->name;
            $this->direccion= $clinica->direccion;
            $this->telefono = $clinica->telefono;
            $this->email = $clinica->email;
            $this->cif = $clinica->cif;
            $this->direccion_fac = $clinica->direccion_fac;
            $this->cuenta = $clinica->cuenta;
        }else{
            $this->editable = false;
            $this->resetForm();
        }
        $this->showModal = true;
    }

    public function save(){
        $this->validate();

        if($this->editable){
            $clinica = Clinica::find($this->clinica_id);
            $clinica->name = $this->name;
            $clinica->direccion = $this->direccion;
            $clinica->telefono = $this->telefono;
            $clinica->email = $this->email;
            $clinica->cif = $this->cif;
            $clinica->direccion_fac = $this->direccion_fac;
            $clinica->cuenta = $this->cuenta;

            $clinica->save();
            $this->dispatch('clinicaSaved', 'Clínica Actualizada');  // Emite un evento para que otros componentes puedan escuchar

        }else{
            $clinica = Clinica::create([
                'name' => $this->name,
                'direccion' => $this->direccion,
                'telefono' => $this->telefono,
                'email' => $this->email,
                'cif' => $this->cif,
                'direccion_fac' => $this->direccion_fac,
                'cuenta' => $this->cuenta,
            ]);

            // Generar una contraseña aleatoria
            $password = Str::random(8);

            // Creamos el usuario asignado a la clínica
            $user = User::create([
                'name' => $this->name,
                'colegiado' => '12345',
                'email' => $this->email,
                'password' => bcrypt($password), // Cambia esto por la lógica de contraseña que desees
                'clinica_id' => $clinica->id, // Relacionamos el usuario con la clínica
            ]);

            // Asignar el rol "admin" al usuario
            $user->assignRole('doctor');

            $clinica_user = ClinicaUser::create([
                'clinica_id' => $clinica->id,
                'user_id' => $user->id
            ]);

            $clinica_user->save();
            // Enviar los datos de acceso al correo del usuario
            Mail::to($this->email)->send(new CredencialesClinica($this->name, $this->email, $password));

            $this->dispatch('clinicaSaved', 'Clinica '. $this->name. ' creada');
        }
        $this->close();
    }

    public function showClinica($clinicaId){
        return redirect()->route('admin.clinica.view', ['id' => $clinicaId]);
    }

    public function deleteClinica($clinicaId)
    {
        $this->dispatch('deleteClinic', ['clinicaId' => $clinicaId]);
    }

    public function deleteClinicConfirmed($clinicaId)
    {
        try {
            $clinica = Clinica::findOrFail($clinicaId); // Usamos findOrFail para lanzar una excepción si no se encuentra
            // Eliminar archivos relacionados
            $this->deleteRelatedFiles($clinica);

            // Eliminamos la clínica
            $clinica->delete();

            // Mensaje de éxito
            session()->flash('message', 'Clínica eliminada exitosamente.');
            $this->dispatchBrowserEvent('clinicaEliminada');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la clínica: ' . $e->getMessage());
        }
    }

    private function deleteRelatedFiles($clinica)
    {
        $folderPath = storage_path('app/public/clinicas/' . $clinica->name);
        if (File::exists($folderPath)) {
            File::deleteDirectory($folderPath); // Eliminar la carpeta de la clínica y sus archivos
        }
    }

    public function resetForm()
    {
        $this->reset(['name',
                'direccion',
                'email',
                'telefono',
                'cif',
                'direccion_fac',
                'cuenta',
            ]);
    }

    public function close()
    {
        $this->showModal = false;
        $this->reset();

    }
}
