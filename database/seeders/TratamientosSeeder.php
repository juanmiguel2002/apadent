<?php

namespace Database\Seeders;

use App\Models\Tratamiento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TratamientosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tratamiento=new Tratamiento;
        $tratamiento->name='Tratamiento 1';
        $tratamiento->descripcion = 'Caso de 15 férulas';
        $tratamiento->save();

        $tratamiento=new Tratamiento;
        $tratamiento->name='Tratamiento 2';
        $tratamiento->descripcion = 'Caso de 15-25 férulas';
        $tratamiento->save();

        $tratamiento=new Tratamiento;
        $tratamiento->name='Tratamiento 3';
        $tratamiento->descripcion = 'Caso de 25-40 férulas';
        $tratamiento->save();

        $tratamiento=new Tratamiento;
        $tratamiento->name='Tratamiento 4';
        $tratamiento->descripcion = 'Caso de 40-60 férulas';
        $tratamiento->save();
    }
}
