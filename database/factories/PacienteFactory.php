<?php

namespace Database\Factories;

use App\Models\Etapa;
use App\Models\Fase;
use App\Models\Paciente;
use App\Models\Tratamiento;
use Illuminate\Database\Eloquent\Factories\Factory;

class PacienteFactory extends Factory
{
    protected $model = Paciente::class;

    public function definition()
    {
        return [
            'num_paciente' => $this->faker->unique()->numberBetween(1000, 9999),
            'name' => $this->faker->firstName,
            'apellidos' => $this->faker->lastName,
            'fecha_nacimiento' => $this->faker->date(),
            'email' => $this->faker->email,
            'telefono' => $this->faker->phoneNumber,
            'observacion' => $this->faker->text(50),
            'obser_cbct' => $this->faker->text(50),
            'url_img' => null,
            'activo' => 1,
            'clinica_id' => $this->faker->numberBetween(1, 2), // Ajusta este valor según el ID de las clínicas
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Paciente $paciente) {
            // Asignar un tratamiento aleatorio de los ya existentes
            $tratamiento = Tratamiento::inRandomOrder()->first();

            // Relacionar paciente con tratamiento en la tabla paciente_trat
            $paciente->tratamientos()->attach($tratamiento->id);

            // Obtener las etapas del tratamiento
            $fases = Fase::where('trat_id', $tratamiento->id)->get();

            // Asignar cada etapa del tratamiento al paciente en la tabla paciente_etapas
            foreach ($fases as $fase) {
                Etapa::create([
                    'name' => 'Inicio',
                    'paciente_id' => $paciente->id,
                    'fase_id' => $fase->id,
                    'fecha_ini' => $this->faker->date(),
                    'fecha_fin' => null,
                    'status' => $this->faker->randomElement(['Set Up', 'En proceso', 'Pausado','Finalizado']),
                ]);
            }
        });
    }
}
