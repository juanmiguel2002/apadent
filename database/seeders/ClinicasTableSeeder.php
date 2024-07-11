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
            'responsable' => 'Carla',
            'email' => 'grupo@gmail.com',
            'telefono' => '690797596',
            'direccion' => 'Plaza Gabriel Miró',
            'localidad' => '03725 - Teulada Alicante'
        ];

        Clinica::insert($clinicas);
    }
}
