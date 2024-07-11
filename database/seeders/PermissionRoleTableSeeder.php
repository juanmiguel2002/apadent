<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = Permission::all();
        // Convertimos a colección de Eloquent si es necesario
        $admin_permissions = Permission::hydrate($permissions->toArray());

        // Asignar todos los permisos al rol de administrador (ID 1)
        Role::findOrFail(1)->permissions()->sync($admin_permissions->pluck('id'));

        // Filtrar permisos para el rol de doctor
        $doctor_permissions = $admin_permissions->filter(function ($permission) {
            return $permission->title != 'admin_access';
        });

        // Asignar permisos filtrados al rol de doctor (ID 2)
        Role::findOrFail(2)->permissions()->sync($doctor_permissions->pluck('id'));

        // Permisos específicos para clinica_user
        $clinica_user_permissions = $admin_permissions->filter(function ($permission) {
            return in_array($permission->title, [
                'paciente_view', 'paciente_create', 'paciente_modify',
                'etapa_create', 'etapa_view', 'factura_view', 'documentacion_add',
                'clinica_access', 'clinica_view'
            ]);
        });

        // Asignar permisos al rol de clinica_user (ID 3)
        Role::findOrFail(3)->permissions()->sync($clinica_user_permissions->pluck('id'));
    }
}
