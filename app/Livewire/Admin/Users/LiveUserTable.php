<?php

namespace App\Livewire\Admin\Users;

use App\Models\Clinica;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class LiveUserTable extends Component
{
    use WithPagination;
    public $search = "";
    public $perPage = 5; //Para filtrar cuando se ve
    public $camp = null; //Para fel campo a ordenar ok
    public $order = null; //Para fel campo a ordenar ascendente o descendente ok
    public $icon = '-sort'; //Para el ícono
    public $user_role = ''; //Para filtrado por rol
    public $roles = []; //Para roles
    public $selectedPermissions = [];
    public $page, $clear;
    public $clinicas;

    public $permissions, $user;
    public $showModal = false, $showPermisos = false;
    public $isEditing = false;
    public $name, $email, $colegiado, $password, $password_confirmation, $selectedClinica;
    public $selectedRole, $userId, $selectedUser; // Para edición

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'colegiado' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($this->isEditing ? $this->userId : 'NULL') . ',id',
            'password' => !$this->isEditing ? 'required|string|min:8|confirmed' : 'nullable|string|min:8|confirmed',
            'selectedRole' => 'required|exists:roles,id',
            'selectedClinica' => 'required|exists:clinicas,id',
        ];
    }

    /*Para mantener persistente los filtros y la búsqueda */
    protected $queryString = [
        'search' => ['except' => ''],
        'camp' => ['except' => null],
        'order' => ['except' => ''],
    ];
    /*********************************************
     * Método para resetear el url de paginación *
     *********************************************/
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
        $this->icon = $this->iconDirection($this->order);
        $roles = Role::all();
        $this->permissions = Permission::all();
        $this->roles = $roles;
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
            // $usersQuery->whereHas('clinicas', function ($query) {
            //     $query->whereIn('clinica_id', auth()->user()->clinicas->pluck('id'));
            // });

            // Si el usuario autenticado tiene el rol de "doctor", filtramos aún más
            if (auth()->user()->hasRole('doctor')) {
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
        return view('livewire.admin.users.live-user-table', [
                    'users' => $users,
                    'roles' => $this->roles,
                    'clinicas' => $this->clinicas]);
    }

    /*METODOS PARA EL CRUD */
    public function showCreateModal($userId = null)
    {
        $this->resetValidation();  // Resetea la validación cuando se abre el modal
        $this->resetFields();  // Resetea los campos del formulario

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
        $this->selectedRole = $user->roles->first()->id ?? null;
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

            $user->roles()->sync([$this->selectedRole]);
            $user->clinicas()->sync([$this->selectedClinica]);
            $this->dispatch('userSaved', 'Usuario Actualizado');  // Emite un evento para que otros componentes puedan escuchar

        } else {
            $user = User::create([
                'name' => $this->name,
                'colegiado' => $this->colegiado,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            $roleId = Role::findById($this->selectedRole);
            dd($roleId);

            $user->roles()->attach($roleId);
            $user->clinicas()->attach($this->selectedClinica);

            $user->sendEmailVerificationNotification();
            $this->dispatch('userSaved', 'Usuario Añadido');  // Emite un evento para que otros componentes puedan escuchar

        }
        $this->close();
    }

    public function close()
    {
        $this->showModal = false;
        // $this->reset();
    }

    // Permissions
    // public function verPermisos($id){
    //     $this->showPermisos = true;
    //     $this->userId = $id;
    //     $this->selectedUser = User::findOrFail($id);

    //     // Cargar los permisos asignados actualmente
    //     $this->selectedPermissions = $this->selectedUser->permissions->pluck('id')->toArray();
    //     // Abrir el modal
    //     $this->showPermisos = true;
    // }

    // public function updatePermissions(){
    //     // Verificamos que un usuario esté seleccionado
    //     if ($this->selectedUser) {
    //         // Sincronizar los permisos seleccionados
    //         $this->selectedUser->syncPermissions($this->selectedPermissions);

    //         // Opcionalmente, puedes emitir un mensaje o redirigir
    //         session()->flash('message', 'Permisos actualizados correctamente.');

    //         $this->showPermisos = false;

    //     }
    // }

    // public function closePermisos(){
    //     $this->showPermisos = false;
    //     $this->selectedUser = null;
    // }


    // Eliminar usuario
    // public function deleteUser($userId)
    // {
    //     $this->dispatch('confirmDelete', ['userId' => $userId]);
    // }

    // public function deleteUserConfirmed($userId)
    // {
    //     $user = User::find($userId);

    //     if ($user) {
    //         $user->delete();
    //         session()->flash('message', 'Usuario eliminado exitosamente.');
    //     } else {
    //         session()->flash('error', 'El usuario no se encontró.');
    //     }
    // }

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
