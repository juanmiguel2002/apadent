<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\User;
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
        Paciente::factory()->count(100)->create();
        // $this->call(TratamientosSeeder::class);
        // $this->call(ClinicasTableSeeder::class);

        // // Crear usuarios
    //     $adminUser = User::factory()->create([
    //         'name' => 'admin',
    //         'colegiado' => '123456',
    //         'password' => bcrypt('admin123'),
    //         'email' => 'admin@admin.com',
    //     ]);

    //     $doctorAdminUser = User::factory()->create([
    //         'name' => 'ClinicaAdmin',
    //         'colegiado' => '1238456',
    //         'password' => bcrypt('juanmi1234'),
    //         'email' => 'juanmi0802@gmail.com',
    //     ]);

    //    // Crear los roles
    //     $admin = Role::create(['name' => 'admin']);
    //     $doctor = Role::create(['name' => 'doctor']);
    //     $clinica = Role::create(['name' => 'clinica']);
    //     $doctorAdmin = Role::create(['name' => 'doctor_admin']);

    //     // Definir permisos
    //     $permissions = [
    //         'clinica_view',
    //         'factura_view',
    //         'paciente_create',
    //         'paciente_update',
    //         'etapa_view',
    //         'etapa_create',
    //         'documentacion_add',
    //         'stripping',
    //         'usuario_read',
    //         'usuario_create',
    //         'usuario_update',
    //         'usuario_delete', //eliminar cuenta user administration
    //         'rol_create',
    //         'rol_update',
    //         'rol_view'
    //     ];

    //     // Crear los permisos
    //     foreach ($permissions as $permission) {
    //         Permission::create(['name' => $permission]);
    //     }

    //     // Asignar permisos al rol "admin" (todos los permisos)
    //     $admin->syncPermissions(Permission::all());

    //     // Asignar permisos específicos al rol "doctor"
    //     $doctorPermissions = [
    //         'clinica_view',
    //         'factura_view',
    //         'paciente_create',
    //         'paciente_update',
    //         'etapa_view',
    //     ];
    //     $doctor->syncPermissions(Permission::whereIn('name', $doctorPermissions)->get());

    //     // Asignar permisos específicos al rol "clinica"
    //     $clinicaPermissions = [
    //         'clinica_view',
    //         'paciente_create',
    //         'paciente_update',
    //         'documentacion_add',
    //         'etapa_view',
    //         'etapa_create'
    //     ];
    //     $clinica->syncPermissions(Permission::whereIn('name', $clinicaPermissions)->get());

    //     // Asignar permisos al rol "doctor_admin" (los permisos del admin pero relacionados con la clínica)
    //     $doctorAdminPermissions = Permission::all()->filter(function ($permission) {
    //         return !in_array($permission->name, ['rol_create', 'rol_update', 'rol_view']); // Opcional: Excluir ciertos permisos si aplica.
    //     });
    //     $doctorAdmin->syncPermissions($doctorAdminPermissions);

    //     // Asignar roles a usuarios
    //     $adminUser->assignRole('admin');
    //     $doctorAdminUser->assignRole('doctor_admin');
        // $doctorUser->assignRole('doctor');
        // $clinicaUser->assignRole('clinica');
    }
}
