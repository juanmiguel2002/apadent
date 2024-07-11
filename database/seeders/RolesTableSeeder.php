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
                'title' => 'admin',
            ],
            [
                'id'    => 2,
                'title' => 'doctor',
            ],
            [
                'id'    => 3,
                'title' => 'clinica_user',
            ],
        ];
        Role::insert($roles);

    }
}
