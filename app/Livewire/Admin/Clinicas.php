<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Mail\CredencialesClinica;
use App\Models\Clinica;
use App\Models\ClinicaUser;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Clinicas extends Component
{
    use WithPagination;
    public $showModal, $editable = false;
    public $clinicas, $clinica_id;
    public $ordenar = '';
    public $search = '';
    public $name, $direccion, $telefono, $email, $cif, $direccion_fac, $cuenta;

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

    protected function rules() {
        return  [
            'name' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required',
            'email' => 'required|email|max:255|unique:clinicas,email,' . ($this->editable ? $this->clinica_id : 'NULL') . ',id',
            'cif' => 'required|string|max:9',
            'direccion_fac' => 'required|string|max:255',
            'cuenta' => 'required|string|max:255',
        ];
    }

    public function mount() {
        // Definir la columna y dirección de ordenación predeterminadas
        $orderByColumn = 'id';
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
            default:
                $orderByColumn = 'id';
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
            $this->resetPage();
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
            $password = Str::password(8);

            // Creamos el usuario asignado a la clínica
            $user = User::create([
                'name' => $this->name. '_admin',
                'colegiado' => Str::random(8),
                'email' => $this->email,
                'password' => bcrypt($password),
            ]);

            // Limpiar roles existentes si es necesario
            $user->syncRoles([]); // Esto eliminará todos los roles existentes

            // Asignar el rol 'doctor_admin' al usuario
            $user->assignRole('doctor_admin');

            ClinicaUser::create([
                'clinica_id' => $clinica->id,
                'user_id' => $user->id
            ]);

            // Enviar los datos de acceso al correo del usuario
            Mail::to($this->email)->send(new CredencialesClinica($this->name, $this->email, $password));

            $this->dispatch('clinicaSaved', 'Clinica '. $this->name . ' creada');
        }
        $this->close();
        $this->mount();
    }

    public function showClinica($clinicaId){
        return redirect()->route('admin.clinica.view', ['id' => $clinicaId]);
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
    }

    // public function confirmDelete($id)
    // {
    //     $clinica = Clinica::with('users', 'pacientes')->find($id);

    //     // Eliminación en cascada de relaciones
    //     if($clinica->usuarios){
    //         foreach ($clinica->usuarios as $usuario) {
    //             $usuario->delete();
    //         }

    //         foreach ($clinica->pacientes as $paciente) {
    //             $paciente->delete();
    //         }
    //     }

    //     $clinicaName = preg_replace('/\s+/', '_', trim($clinica->name));
    //     // Eliminar la carpeta de la clínica (asumiendo una estructura de carpetas definida)
    //     $carpetaClinica = public_path('clinicas/' . $clinicaName);
    //     if (is_dir($carpetaClinica)) {
    //         // Utilizar una librería como File para una eliminación recursiva más segura
    //         File::deleteDirectory($carpetaClinica);
    //     }

    //     // Eliminar el registro de la clínica
    //     $clinica->delete();

    //     // Disparar el evento
    //     $this->dispatch('clinicaEliminada', 'La Clínica '.$clinica->name.' ha sido eliminada exitosamente.');
    //     $this->resetPage();
    // }
}
