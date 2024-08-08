<?php

namespace Database\Factories;

use App\Models\Etapa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Imagen>
 */
class ImagenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'etapa_id' => Etapa::factory(),
            'ruta' => $this->faker->imageUrl(),
        ];
    }
}
