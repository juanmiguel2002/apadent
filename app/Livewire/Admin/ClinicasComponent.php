<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Mail\CredencialesClinica;
use App\Models\Carpeta;
use App\Models\Clinica;
use App\Models\ClinicaUser;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class ClinicasComponent extends Component
{
    use WithPagination;

    public $showModal, $editable = false;
    public $clinica_id;
    public $ordenar = '';
    public $search = '';
    public $name, $direccion, $telefono, $email, $cif, $direccion_fac, $cuenta;

    protected $queryString = [
        'search' => ['except' => ''],
        'ordenar' => ['except' => ''],
    ];

    protected function rules() {
        return  [
            'name' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string',
            'email' => 'required|email|max:255|unique:clinicas,email,' . ($this->editable ? $this->clinica_id : 'NULL') . ',id',
            'cif' => 'required|string|max:9',
            'direccion_fac' => 'required|string|max:255',
            'cuenta' => 'required|string|max:18',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingOrdenar()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orderByColumn = $this->ordenar === 'recientes' ? 'clinicas.created_at' : ($this->ordenar === 'name' ? 'clinicas.name' : 'clinicas.id');
        $orderByDirection = $this->ordenar === 'recientes' ? 'desc' : 'asc';

        $clinicas = Clinica::with('users','pacientes')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy($orderByColumn, $orderByDirection)
            ->paginate(10); // Puedes cambiar 10 por la cantidad de elementos por página
        

        return view('livewire.admin.clinicas', ['clinicas' => $clinicas]);
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
            // Actualizar la clínica existente
            $clinica = Clinica::find($this->clinica_id);
            $clinica->name = $this->name;
            $clinica->direccion = $this->direccion;
            $clinica->telefono = $this->telefono;
            $clinica->email = $this->email;
            $clinica->cif = $this->cif;
            $clinica->direccion_fac = $this->direccion_fac;
            $clinica->cuenta = $this->cuenta;

            $clinica->save();
            // Actualizar carpeta
            $carpeta = Carpeta::where('clinica_id', $this->clinica_id)->first();
            if($carpeta){
                $carpetaName = preg_replace('/\s+/', '_', trim($this->name));

                $carpeta->nombre = $carpetaName;
                $carpeta->save();
            }
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
        $this->dispatch('recargar-pagina');  // Emite un evento para que otros componentes puedan escuchar
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

}
