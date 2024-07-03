<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'name'           => 'Admin',
                'email'          => 'admin@admin.com',
                'password'       => bcrypt('12345678'),
                'remember_token' => null,
            ],
            [
                'id'             => 2,
                'name'           => 'Doctor',
                'email'          => 'doctor@doctor.com',
                'password'       => bcrypt('12345'),
                'remember_token' => null,
            ],
            [
                'id'             => 3,
                'name'           => 'clinica',
                'email'          => 'clinica@apadent.com',
                'password'       => bcrypt('1234'),
                'remember_token' => null,
            ],
        ];

        User::insert($users);
    }
}
