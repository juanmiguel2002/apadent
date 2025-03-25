<?php

namespace App\Livewire\Admin\Users;

use App\Models\Clinica;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UsersManagement extends Component
{
    use WithPagination;

    public $search = "";
    public $perPage = 5; //Para filtrar cuando se ve
    public $camp = null; //Para fel campo a ordenar ok
    public $order = null; //Para fel campo a ordenar ascendente o descendente ok
    public $icon = '-sort'; //Para el ícono
    public $user_role = ''; //Para filtrado por rol
    public $roles = []; //Para roles

    public $clinicas; // todas las clínicas (admin)
    public $clinica_id, $user_clinica; // clinica id del usuario admin de la clínica

    public $permissions, $user, $role; //permissions
    public $showModal = false;
    public $showPermisos = false, $selectedPermissions = [];
    public $isEditing = false;
    public $name, $email, $colegiado, $password, $password_confirmation, $selectedClinica;
    public $selectedRole, $userId; // Para edición

    protected function rules()
    {
        return [
            'name' => 'required|string|max:250',
            'colegiado' => 'required|string|max:50|unique:users,colegiado,' . ($this->isEditing ? $this->userId : 'NULL') . ',id',
            'email' => 'required|email|max:255|unique:users,email,' . ($this->isEditing ? $this->userId : 'NULL') . 'id',
            'password' => !$this->isEditing ? 'required|string|min:8|confirmed' : 'nullable|string|min:8|confirmed',
            'selectedRole' => 'required|exists:roles,name',
            'selectedClinica' => $this->selectedClinica ? 'required|exists:clinicas,id' : '',
        ];
    }

    protected function messages()
    {
        return [
            'colegiado.unique' => 'El número de colegiado ya está registrado. Por favor, verifica la información.',
            'name.required' => 'El nombre es obligatorio.',
            'email.unique' => 'El correo electrónico ya está en uso.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ];
    }

    /*Para mantener persistente los filtros y la búsqueda */
    protected $queryString = [
        'search' => ['except' => ''],
        'camp' => ['except' => null],
        'order' => ['except' => ''],
    ];

    /* Método para resetear el url de paginación **/
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->user_clinica = auth()->user();
        $this->clinica_id = $this->user_clinica->clinicas()->first()?->id;

        $this->icon = $this->iconDirection($this->order);
        // Verificar si el usuario autenticado es 'admin'
        if (auth()->user()->hasRole('admin')) {
            // Si es admin, puede ver todos los roles
            $this->roles = Role::all();
        } elseif (auth()->user()->hasRole('doctor_admin')) {
            // Si es 'doctor_admin', solo puede ver 'doctor' y 'clinica'
            $this->roles = Role::whereIn('name', ['doctor', 'clinica'])->get();
        }
        $this->permissions = Permission::all();
    }

    public function render()
    {
        // Iniciamos la consulta para los usuarios
        $usersQuery = User::query();

        // Filtramos los usuarios por el rol si está definido
        if ($this->user_role !== '') {
            $usersQuery->role($this->user_role);
        }

        // Filtramos los usuarios por la clínica asignada al usuario autenticado
        if (auth()->user()->hasRole('admin')) {
            $this->clinicas = Clinica::all();
        } else {

            // Si el usuario autenticado tiene el rol de "doctor", filtramos aún más
            if (auth()->user()->hasRole('doctor_admin')) {
                $usersQuery->whereHas('clinicas', function ($query) {
                    $query->whereIn('clinica_id', auth()->user()->clinicas->pluck('id'));
                });
            }
        }

        // Búsqueda por nombre de usuario
        if (!empty($this->search)) {
            $usersQuery->where('name', 'like', '%' . $this->search . '%')
            ->orWhereHas('clinicas', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }

        // Ordenar por columna y orden definidos, si existen
        if ($this->camp && $this->order) {
            $usersQuery->orderBy($this->camp, $this->order);
        } else {
            $this->camp = null;
            $this->order = null;
        }

        // Paginación
        $users = $usersQuery->paginate($this->perPage);
        // Devolvemos la vista con los usuarios filtrados
        return view('livewire.admin.users.users-management', [
                    'users' => $users,
                    'roles' => $this->roles,
                    'clinicas' => $this->clinicas]);
    }

    /*METODOS PARA EL CRUD */
    public function showCreateModal($userId = null)
    {
        $this->resetFields();  // Resetea los campos del formulario
        $this->resetValidation();  // Resetea la validación cuando se abre el modal

        $this->userId = $userId;

        if ($userId) {
            $this->isEditing = true;
            $this->loadUser();  // Carga los datos del usuario para editar
        } else {
            $this->isEditing = false;
        }

        $this->showModal = true;
    }

    public function loadUser()
    {
        $user = User::findOrFail($this->userId);
        $this->name = $user->name;
        $this->colegiado = $user->colegiado;
        $this->email = $user->email;
        $this->selectedRole = $user->roles->first()->name ?? null;
        $this->selectedClinica = $user->clinicas->first()->id ?? null;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $user = User::find($this->userId);
            $user->name = $this->name;
            $user->colegiado = $this->colegiado;
            $user->email = $this->email;

            if ($this->password) {
                $user->password = Hash::make($this->password);
            }

            $user->save();

            $user->assignRole($this->selectedRole);
            $user->clinicas()->sync([$this->selectedClinica]);
            $this->dispatch('userSaved', 'Usuario Actualizado');  // Emite un evento para que otros componentes puedan escuchar

        } else {
            $user = User::create([
                'name' => $this->name,
                'colegiado' => $this->colegiado,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->assignRole($this->selectedRole);
            $user->clinicas()->attach($this->selectedClinica ? $this->selectedClinica : $this->clinica_id);

            $user->sendEmailVerificationNotification();
            $this->dispatch('userSaved', 'Usuario Añadido');  // Emite un evento para que otros componentes puedan escuchar

        }
        $this->close();
    }

    // Permisos check rol
    public function verPermisos($user,$roleId) {
        $this->showPermisos = true;
        $this->user = $user;

        // Cargar los permisos asociados al rol
        $this->role = Role::find($roleId);

        if ($this->role) {
            foreach ($this->role->permissions as $permission) {
                $this->selectedPermissions[$permission->id] = [
                    'check' => true, // Marca como seleccionado
                ];
            }
        }
    }

    public function savePermissions()
    {
        if ($this->role) {
            $permissionsToSync = array_keys(array_filter($this->selectedPermissions, function ($permission) {
                return $permission['check'] ?? false;
            }));

            // Sincroniza los permisos
            $this->role->permissions()->sync($permissionsToSync);

            session()->flash('message', 'Permisos actualizados correctamente.');
        }
        $this->close();
    }

    public function close()
    {
        if($this->showModal){
            $this->showModal = false;
        }else{
            $this->showPermisos = false;
        }
    }

    //Método para limpiar y ordenar icon
    public function resetFields()
    {
        $this->name = '';
        $this->colegiado = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedRole = '';
        $this->selectedClinica = '';
    }

    public function sortable($camp)
    {
        if ($camp !== $this->camp) {
            $this->order = null;
        }
        // dd($camp);
        switch ($this->order) {
            case null:
                $this->order = 'asc';
                // $this->icon = '-sort-up';
                break;
            case 'asc':
                $this->order = 'desc';
                // $this->icon = '-sort-down';
                break;
            case 'desc':
                $this->order = null;
                // $this->icon = '-sort';
                break;
            default:
                $this->order = 'asc';
                // $this->icon = '-sort-up';
                break;
        }
        // Actualizamos el campo a nivel global
        $this->icon = $this->iconDirection($this->order);
        $this->camp = $camp;
    }

    public function iconDirection($sort): string
    {
        if (!$sort) {
            return '-sort';
        }
        return $sort === 'asc' ? '-sort-up' : '-sort-down';
    }
}
