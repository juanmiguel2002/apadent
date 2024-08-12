<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        // Obtener todos los permisos
        $permissions = Permission::all();

        // Asignar todos los permisos al rol de administrador (ID 1)
        $adminRole = Role::findOrFail(1);
        $adminRole->syncPermissions($permissions);

        // Filtrar permisos para el rol de doctor
        $doctorPermissions = $permissions->filter(function ($permission) {
            return $permission->name != 'admin_access';
        });

        // Asignar permisos filtrados al rol de doctor (ID 2)
        $doctorRole = Role::findOrFail(2);
        $doctorRole->syncPermissions($doctorPermissions);

        // Permisos específicos para clinica_user
        $clinicaUserPermissions = $permissions->filter(function ($permission) {
            return in_array($permission->name, [
                'admin_access',
                'clinica_access',
                'paciente_delete',
                'etapa_revise',
                'doctor_user',
                'paciente_create',
                'create_user',
                'read_user',
                'update_user',
                'delete_user'
            ]);
        });

        // Asignar permisos al rol de clinica_user (ID 3)
        $clinicaUserRole = Role::findOrFail(3);
        $clinicaUserRole->syncPermissions($clinicaUserPermissions);
    }
}
