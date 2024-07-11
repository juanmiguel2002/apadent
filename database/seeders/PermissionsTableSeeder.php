<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

    class PermissionsTableSeeder extends Seeder
    {
        public function run()
        {
            $permissions = [
                ['id' => 1, 'title' => 'admin_access'], //admin access
                ['id' => 2, 'title' => 'clinica_access'], //clinica access
                ['id' => 3, 'title' => 'paciente_view'], //paciente view
                ['id' => 4, 'title' => 'paciente_delete'],//paciente eliminar (doctor)
                ['id' => 5, 'title' => 'etapa_revise'], //modificar etapa (doctor)
                ['id' => 6, 'title' => 'doctor_user'], // doctor access
                ['id' => 7, 'title' => 'clinica_view'],// ver datos clínica (doctor, clinica_user)
                // Añadimos permisos adicionales necesarios para clinica_user
                ['id' => 8, 'title' => 'paciente_create'],
                ['id' => 9, 'title' => 'paciente_modify'],
                ['id' => 10, 'title' => 'etapa_create'],
                ['id' => 11, 'title' => 'etapa_view'],
                ['id' => 12, 'title' => 'factura_view'],
                ['id' => 13, 'title' => 'documentacion_add'],
            ];

            Permission::insert($permissions);
        }
    }
