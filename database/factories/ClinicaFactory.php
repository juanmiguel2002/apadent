<?php

namespace Database\Factories;

use App\Models\Clinica;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Clinica>
 */
class ClinicaFactory extends Factory
{
    protected $model = Clinica::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'direccion' => $this->faker->address,
            'telefono' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'cif' => strtoupper($this->faker->bothify('B########')),
            'cuenta' => $this->faker->bankAccountNumber,
        ];
    }

}
