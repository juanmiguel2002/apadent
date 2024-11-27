<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'id'    => 1,
                'name' => 'admin',
                'guard_name' => 'web',
            ],
            [
                'id'    => 2,
                'name' => 'doctor',
                'guard_name' => 'web',
            ],
            [
                'id'    => 3,
                'name' => 'clinica_user',
                'guard_name' => 'web',
            ],
            [
                'id'    => 4,
                'name' => 'doctor_admin',
                'guard_name' => 'web',
            ],
        ];
        Role::insert($roles);

    }
}
