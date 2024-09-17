<?php

namespace Database\Factories;

use App\Models\Tratamiento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Etapa>
 */
class EtapaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trat_id' => Tratamiento::inRandomOrder()->limit(1)->pluck('id')->first(),
            'name' => $this->faker->word,
            'status' => $this->faker->randomElement(['Set Up','En proceso','Pausado','Finalizado']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
