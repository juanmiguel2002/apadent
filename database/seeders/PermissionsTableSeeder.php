<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

    class PermissionsTableSeeder extends Seeder
    {
        public function run()
        {
            $permissions = [
                ['id' => 1, 'name' => 'admin_access'], //admin access
                ['id' => 2, 'name' => 'clinica_access'], //clinica access
                ['id' => 3, 'name' => 'paciente_view'], //paciente view
                ['id' => 4, 'name' => 'paciente_delete'],//paciente eliminar (doctor)
                ['id' => 5, 'name' => 'etapa_revise'], //modificar etapa (doctor)
                ['id' => 6, 'name' => 'doctor_user'], // doctor access
                ['id' => 7, 'name' => 'clinica_view'],// ver datos clínica (doctor, clinica_user)
                // Añadimos permisos adicionales necesarios para clinica_user
                ['id' => 8, 'name' => 'paciente_create'],
                ['id' => 9, 'name' => 'paciente_modify'],
                ['id' => 10, 'name' => 'etapa_create'],
                ['id' => 11, 'name' => 'etapa_view'],
                ['id' => 12, 'name' => 'factura_view'],
                ['id' => 13, 'name' => 'documentacion_add'],
            ];

            Permission::insert($permissions);
        }
    }
