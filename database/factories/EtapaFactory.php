<?php

namespace Database\Factories;

use App\Models\Etapa;
use App\Models\Fase;
use App\Models\Paciente;
use App\Models\Tratamiento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Etapa>
 */
class EtapaFactory extends Factory
{
    protected $model = Etapa::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'fecha_ini' => $this->faker->date(),
            'fecha_fin' => $this->faker->optional()->date(),
            'status' => $this->faker->randomElement(['Set Up', 'En proceso', 'Pausado', 'Finalizado']),
            'revision' => $this->faker->optional()->date(),
            'fase_id' => $this->faker->numberBetween(1, 4),
            'trat_id' => Tratamiento::inRandomOrder()->first()->id ?? 1, // Asignamos un tratamiento existente
            'paciente_id' => Paciente::inRandomOrder()->first()->id ?? 1, // Asignamos un paciente existente
        ];
    }

}
