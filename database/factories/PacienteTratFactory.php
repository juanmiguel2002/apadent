<?php

namespace Database\Factories;

use App\Models\Paciente;
use App\Models\Tratamiento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PacienteTrat>
 */
class PacienteTratFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'paciente_id' => Paciente::factory(),
            'trat_id' => Tratamiento::inRandomOrder()->limit(1)->pluck('id')->first(),
            'updated_at' => $this->faker->date(),
            'created_at' => $this->faker->date(),
        ];

    }   
}
