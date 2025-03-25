<?php

namespace App\Livewire\Admin;

use Spatie\Permission\Models\Permission;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class PermissionsManager extends Component
{
    public $roles;
    public $permissions;
    public $selectedRole;
    public $permissionName = '';

    public function mount()
    {
        $this->roles = Role::all();
        $this->permissions = Permission::all();
    }

    public function render()
    {
        return view('livewire.admin.permissions-manager');
    }

    public function createPermission()
    {
        $this->validate([
            'permissionName' => 'required|string|unique:permissions,name',
        ],[
            'permissionName.required' => 'El nombre del permiso es obligatorio.',
            'permissionName.unique' => 'El permiso ya existe.',
        ]);

        Permission::create([
            'name' => $this->permissionName,
            'guard_name' => 'web'
        ]);
        $this->dispatch('recargar-pagina');
        $this->permissionName = '';
    }

    public function deletePermission($permissionId)
    {
        Permission::find($permissionId)->delete();
        $this->permissions = Permission::all();
    }
}
