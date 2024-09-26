<?php

namespace Database\Factories;

use App\Models\PacienteEtapas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PacienteEtapas>
 */
class PacienteEtapasFactory extends Factory
{
    protected $model = PacienteEtapas::class;

    public function definition()
    {
        return [
            'etapa_id' => \App\Models\Etapa::factory(),
            'fecha_ini' => $this->faker->date(),
            'fecha_fin' => $this->faker->date(),
            'status' => $this->faker->randomElement(['Set Up', 'En progreso', 'Pausado','Finalizado']),
        ];
    }
}
