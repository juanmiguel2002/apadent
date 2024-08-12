<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

    class PermissionsTableSeeder extends Seeder
    {
        public function run()
        {
            $permissions = [
                ['name' => 'admin_access', 'guard_name' => 'web'],
                ['name' => 'clinica_access', 'guard_name' => 'web'],
                ['name' => 'paciente_view', 'guard_name' => 'web'],
                ['name' => 'paciente_delete', 'guard_name' => 'web'],
                ['name' => 'etapa_revise', 'guard_name' => 'web'],
                ['name' => 'doctor_user', 'guard_name' => 'web'],
                ['name' => 'clinica_view', 'guard_name' => 'web'],
                ['name' => 'paciente_create', 'guard_name' => 'web'],
                ['name' => 'paciente_modify', 'guard_name' => 'web'],
                ['name' => 'etapa_create', 'guard_name' => 'web'],
                ['name' => 'etapa_view', 'guard_name' => 'web'],
                ['name' => 'factura_view', 'guard_name' => 'web'],
                ['name' => 'documentacion_add', 'guard_name' => 'web'],
                ['name' => 'usuario_create', 'guard_name' => 'web'],
                ['name' => 'usuario_read', 'guard_name' => 'web'],
                ['name' => 'usuario_update', 'guard_name' => 'web'],
                ['name' => 'usuario_delete', 'guard_name' => 'web'],
                ['name' => 'role_create', 'guard_name' => 'web'],
                ['name' => 'role_read', 'guard_name' => 'web'],
                ['name' => 'role_update', 'guard_name' => 'web'],
                ['name' => 'role_delete', 'guard_name' => 'web'],
            ];

            foreach ($permissions as $permission) {
                Permission::create($permission);
            }
        }
    }
