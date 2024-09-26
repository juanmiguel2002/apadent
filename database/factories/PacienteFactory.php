<?php

namespace Database\Factories;

use App\Models\Paciente;
use App\Models\PacienteEtapas;
use App\Models\Tratamiento;
use App\Models\TratamientoEtapa;
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
            'num_paciente' => $this->faker->unique()->randomNumber(),
            'name' => $this->faker->firstName,
            'apellidos' => $this->faker->lastName,
            'fecha_nacimiento' => $this->faker->date(),
            'email' => $this->faker->email,
            'telefono' => $this->faker->phoneNumber,
            'observacion' => $this->faker->sentence(),
            'clinica_id' => 1, // Relación con clínica
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
            $etapasTratamiento = TratamientoEtapa::where('trat_id', $tratamiento->id)->get();

            // Asignar cada etapa del tratamiento al paciente en la tabla paciente_etapas
            foreach ($etapasTratamiento as $etapaTratamiento) {
                PacienteEtapas::create([
                    'paciente_id' => $paciente->id,
                    'etapa_id' => 1,
                    'fecha_ini' => $this->faker->date(),
                    'fecha_fin' => $this->faker->optional()->date(),
                    'status' => $this->faker->randomElement(['Set Up', 'En progreso', 'Pausado','Finalizado']),
                ]);
            }
        });
    }
}
