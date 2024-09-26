<?php

namespace Database\Seeders;

use App\Models\Archivo;
use App\Models\Etapa;
use App\Models\Factura;
use App\Models\Imagen;
use App\Models\Mensaje;
use App\Models\Paciente;
use App\Models\PacienteEtapas;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;
use App\Models\TratamientoEtapa;
use App\Models\User;
use Database\Factories\PacienteTratFactory;
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
        $pacientes = Paciente::all();

        // Obtener todos los tratamientos
        $tratamientos = Tratamiento::all();

        foreach ($pacientes as $paciente) {
            // Asignar un tratamiento aleatorio
            $tratamiento = $tratamientos->random();

            // Relacionar el paciente con el tratamiento en la tabla "paciente_trat"
            $paciente->tratamientos()->attach($tratamiento->id);

            // Obtener las etapas del tratamiento desde la tabla "tratamiento_etapa"
            $etapasTratamiento = TratamientoEtapa::where('trat_id', $tratamiento->id)->get();

            // Crear la relación de paciente con cada etapa en la tabla "paciente_etapas"
            foreach ($etapasTratamiento as $etapaTratamiento) {
                PacienteEtapas::create([
                    'paciente_id' => $paciente->id,
                    'etapa_id' => $etapaTratamiento->etapa_id,
                    'fecha_ini' => now(), // Asigna la fecha actual como fecha de inicio
                    'fecha_fin' => now()->addDays(rand(1, 100)), // Fecha de finalización aleatoria
                    'status' => 'Set Up', // O puedes usar un estado aleatorio
                ]);
            }
        }

        // $this->call(RolesTableSeeder::class);

        // // Crear usuarios
        // User::factory()->create([
        //     'name' => 'admin',
        //     'colegiado' => '123456',
        //     'password' => bcrypt('admin123'),
        //     'email' => 'admin@admin.com',
        // ]);

        // User::factory()->create([
        //     'name' => 'doctor',
        //     'colegiado' => '1234546',
        //     'password' => bcrypt('doctor123'),
        //     'email' => 'doctor@doctor.com',
        // ]);

        // User::factory()->create([
        //     'name' => 'clinica_user',
        //     'colegiado' => '1233456',
        //     'password' => bcrypt('clinica123'),
        //     'email' => 'clinica@clinica.com',
        // ]);

        // Crear roles
        // $adminRole = Role::findById(1);
        // $doctorRole = Role::findById(2);
        // $clinicaUserRole = Role::findById(3);
        // $this->call(PermissionsTableSeeder::class);

        // Asignar permisos a los roles
        // $adminRole->syncPermissions(Permission::whereIn('name', [
        //     'admin_access',
        //     'clinica_access',
        //     'paciente_view',
        //     'paciente_delete',
        //     'etapa_revise',
        //     'doctor_user',
        //     'clinica_view',
        //     'paciente_create',
        //     'paciente_modify',
        //     'etapa_create',
        //     'etapa_view',
        //     'factura_view',
        //     'documentacion_add',
        //     'usuario_create',
        //     'usuario_read',
        //     'usuario_update',
        //     'usuario_delete',
        //     'role_create',
        //     'role_read',
        //     'role_update',
        //     'role_delete',
        // ])->get());

        // $doctorRole->syncPermissions(Permission::whereIn('name', [
        //     'paciente_view',
        //     'paciente_delete',
        //     'etapa_revise',
        //     'clinica_view',
        //     'usuario_create',
        //     'usuario_read',
        //     'usuario_update',
        // ])->get());

        // $clinicaUserRole->syncPermissions(Permission::whereIn('name', [
        //     'paciente_view',
        //     'paciente_modify',
        //     'etapa_create',
        //     'etapa_view',
        //     'clinica_view',
        //     'factura_view',
        //     'documentacion_add',
        // ])->get());
        // $this->call(ClinicasTableSeeder::class);

        // Asignar roles a los usuarios
        // User::find(1)->assignRole('admin');
        // User::find(2)->assignRole('doctor');
        // User::find(3)->assignRole('clinica_user');

    }
}
