<?php

namespace Database\Seeders;

use App\Models\Clinica;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClinicasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $clinicas = [
            'name' => 'Grupo Apadet Dental Clinic',
            'direccion' => 'Plaza Gabriel Miró - 03725 - Teulada, Alicante',
            'telefono' => '690797596',
            'email' => 'grupo@gmail.com',

        ];

        Clinica::insert($clinicas);
    }
}
