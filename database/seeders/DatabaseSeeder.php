<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // PermissionsTableSeeder::class,
            // RolesTableSeeder::class,
            // PermissionRoleTableSeeder::class,
            // UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            // ClinicasTableSeeder::class,
            // ClinicaUserTableSeeder::class,
            // TratamientosSeeder::class,
        ]);
        // Crear roles
        $adminRole = Role::create(['name' => 'admin']);
        $doctorRole = Role::create(['name' => 'doctor']);
        $clinicaUserRole = Role::create(['name' => 'clinica_user']);

        // Crear permisos
        $permissions = [
            'admin_access',
            'clinica_access',
            'paciente_view',
            'paciente_delete',
            'etapa_revise',
            'doctor_user',
            'clinica_view',
            'paciente_create',
            'paciente_modify',
            'etapa_create',
            'etapa_view',
            'factura_view',
            'documentacion_add'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Asignar permisos a roles
        $adminRole->givePermissionTo(Permission::all());
        $doctorRole->givePermissionTo(['clinica_access', 'paciente_view', 'paciente_delete', 'etapa_revise', 'doctor_user']);
        $clinicaUserRole->givePermissionTo(['paciente_view', 'paciente_create', 'paciente_modify', 'etapa_create', 'etapa_view', 'factura_view', 'documentacion_add']);
    }
}
