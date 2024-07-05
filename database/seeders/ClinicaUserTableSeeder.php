<?php

namespace Database\Seeders;

use App\Models\Clinica;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClinicaUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         // Asignar usuarios a la Clinica 1
        $clinica1 = Clinica::findOrFail(1);
        $clinica1->users()->sync([2, 3]); // IDs de usuarios clinica y doctor para Clinica 1
    }
}
