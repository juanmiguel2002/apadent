<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_access', // user access => admin
            ],
            [
                'id'    => 2,
                'title' => 'clinica_access', // clinica access => admin, doctor, (tabla clinica)
            ],
            [
                'id'    => 3,
                'title' => 'paciente_access', // paciente access => clinica, doctor
            ],
            [
                'id'    => 4,
                'title' => 'etapa_revise', // etapa revise => doctor, (modificar etapa)
            ],

        ];

        Permission::insert($permissions);
    }
}
