<?php

namespace Database\Factories;

use App\Models\Clinica;
use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paciente>
 */
class PacienteFactory extends Factory
{
    protected $model = Paciente::class;

    public function definition()
    {
        return [
            'num_paciente' => $this->faker->unique()->numerify('PAC####'),
            'name' => $this->faker->name,
            'fecha_nacimiento' => $this->faker->date(),
            'email' => $this->faker->unique()->safeEmail,
            'telefono' => $this->faker->phoneNumber,
            'clinica_id' => 1,
            'updated_at' => $this->faker->dateTime( 'now'),
            'created_at' => $this->faker->dateTime( 'now'),
        ];
    }
}
