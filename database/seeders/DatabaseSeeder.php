<?php

namespace Database\Seeders;

use App\Models\Clinica;
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

        $this->call(RolesTableSeeder::class);

        // // Crear usuarios
        $admin = User::factory()->create([
            'name' => 'admin',
            'colegiado' => '123456',
            'password' => bcrypt('admin123'),
            'email' => 'admin@admin.com',
        ]);

        $doctor = User::factory()->create([
            'name' => 'doctor',
            'colegiado' => '1234546',
            'password' => bcrypt('doctor123'),
            'email' => 'doctor@doctor.com',
        ]);

        $clinica = User::factory()->create([
            'name' => 'clinica',
            'colegiado' => '1233456',
            'password' => bcrypt('clinica123'),
            'email' => 'clinica@clinica.com',
        ]);

        $clinicaAd = User::factory()->create([
            'name' => 'ClinicaAdmin',
            'colegiado' => '1238456',
            'password' => bcrypt('juanmi1234'),
            'email' => 'juanmi0802@gmail.com',
        ]);

        $clinica1 = Clinica::findOrFail(1);
        $clinica1->users()->sync([2, 3, 4]); // IDs de usuarios clinica y doctor para Clinica 1

        // Bucamos los roles
        $adminRole = Role::findById(1);
        $doctorRole = Role::findById(2);
        $clinicaUserRole = Role::findById(3);
        $clinicaAdmin = Role::findById(4);

        $this->call(PermissionsTableSeeder::class);

        $permissions = Permission::all();

        // Asignar permisos a los roles super admin y admin clinica
        $adminRole->syncPermissions($permissions);
        $clinicaAdmin->syncPermissions($permissions);

        // Filtrar permisos para el rol de doctor
        $doctorRole->syncPermissions(Permission::whereIn('name', [
            'doctor_user',
            'paciente_view',
            'paciente_create',
            'paciente_modify',
            'etapa_view',
            'clinica_view',
            'factura_view',
            'documentacion_add',
        ])->get());

        $clinicaUserRole->syncPermissions(Permission::whereIn('name', [
            'clinica_access',
            'clinica_view',
            'paciente_view',
            'paciente_create',
            'paciente_modify',
            'etapa_create',
            'etapa_view',
            'clinica_view',
            'factura_view',
            'documentacion_add',
        ])->get());
        // $this->call(ClinicasTableSeeder::class);

        // Asignar roles a los usuarios
        $admin->assignRole('admin');
        $clinicaAd->assignRole('doctor_admin');
        $doctor->assignRole('doctor');
        $clinica->assignRole('clinica_user');
    }
}
