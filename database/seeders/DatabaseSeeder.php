<?php

namespace Database\Seeders;

use App\Models\Archivo;
use App\Models\Etapa;
use App\Models\Factura;
use App\Models\Imagen;
use App\Models\Mensaje;
use App\Models\Paciente;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;
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

        // Crear usuarios
        // $admin = User::factory()->create([
        //     'name' => 'admin',
        //     'password' => bcrypt('admin123'),
        //     'email' => 'admin@admin.com',
        // ]);

        // $doctor = User::factory()->create([
        //     'name' => 'doctor',
        //     'password' => bcrypt('doctor123'),
        //     'email' => 'doctor@doctor.com',
        // ]);

        // $clinicaUser = User::factory()->create([
        //     'name' => 'clinica_user',
        //     'password' => bcrypt('clinica123'),
        //     'email' => 'clinica@clinica.com',
        // ]);

        // Crear roles
        $adminRole = Role::findById(1);
        $doctorRole = Role::findById(2);
        $clinicaUserRole = Role::findById(3);
        // $this->call(PermissionsTableSeeder::class);

        // Asignar permisos a los roles
        $adminRole->syncPermissions(Permission::whereIn('name', [
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
            'documentacion_add',
            'usuario_create',
            'usuario_read',
            'usuario_update',
            'usuario_delete',
            'role_create',
            'role_read',
            'role_update',
            'role_delete',
        ])->get());

        $doctorRole->syncPermissions(Permission::whereIn('name', [
            'paciente_view',
            'paciente_delete',
            'etapa_revise',
            'clinica_view',
            'usuario_create',
            'usuario_read',
            'usuario_update',
        ])->get());

        $clinicaUserRole->syncPermissions(Permission::whereIn('name', [
            'paciente_view',
            'paciente_modify',
            'etapa_create',
            'etapa_view',
            'clinica_view',
            'factura_view',
            'documentacion_add',
        ])->get());

        // Asignar roles a los usuarios
        // User::findId(1)->assignRole('admin');
        // User::findId(2)->assignRole('doctor');
        // User::findId(3)->assignRole('clinica_user');

    }
    // public function run()
    // {
    //     // Crear pacientes
    //     $pacientes = Paciente::factory()->count(100)->create();

    //     // Obtener tratamientos existentes
    //     $tratamientos = Tratamiento::all(); // Asegúrate de que haya tratamientos en la base de datos

    //     // Crear relaciones muchos a muchos entre pacientes y tratamientos
    //     foreach ($pacientes as $paciente) {
    //         // Seleccionar algunos tratamientos al azar sin duplicados
    //         $tratamientosRandom = $tratamientos->random(rand(1, 5))->unique();

    //         foreach ($tratamientosRandom as $tratamiento) {
    //             // Crear relación en la tabla pivote
    //             $pacienteTrat = PacienteTrat::factory()->create([
    //                 'paciente_id' => $paciente->id,
    //                 'trat_id' => $tratamiento->id,
    //             ]);

    //             // Crear etapas para cada tratamiento
    //             $etapas = Etapa::factory()->count(rand(1, 3))->create([
    //                 'trat_id' => $pacienteTrat->trat_id,
    //             ]);

    //             // Crear imágenes, archivos y mensajes para cada etapa
    //             foreach ($etapas as $etapa) {
    //                 Imagen::factory()->count(rand(1, 5))->create(['etapa_id' => $etapa->id]);
    //                 Archivo::factory()->count(rand(1, 5))->create(['etapa_id' => $etapa->id]);
    //                 Mensaje::factory()->count(rand(1, 5))->create([
    //                     'etapa_id' => $etapa->id,
    //                     'user_id' => User::all()->random()->id,
    //                 ]);
    //             }
    //         }
    //     }
    //     Factura::factory()->count(10)->create();
    // }

}
