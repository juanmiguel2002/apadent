<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RoleUserTableSeeder extends Seeder
{
    public function run()
    {
        User::findOrFail(1)->roles()->sync(1); // admin
        User::findOrFail(2)->roles()->sync(3); // Doctor
        User::findOrFail(3)->roles()->sync(2); //user_clinica

    }
}
