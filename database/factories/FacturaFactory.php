<?php

namespace Database\Factories;

use App\Models\Clinica;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Factura>
 */
class FacturaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'clinica_id' => 1,
            'user_id' => 2,
            'ruta' => $this->faker->filePath(),
        ];
    }
}
