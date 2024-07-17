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
            ],
            [
                'id'    => 2,
                'name' => 'doctor',
            ],
            [
                'id'    => 3,
                'name' => 'clinica_user',
            ],
        ];
        Role::insert($roles);

    }
}
