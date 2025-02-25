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
            'clinica_id' => $this->faker->numberBetween(1, 27),
            // Este es un ejemplo, debe ser cambiado según su necesidad.
        ];
    }

    // public function configure()
    // {
    //     return $this->afterCreating(function (Paciente $paciente) {
    //         // Obtener un ID de tratamiento aleatorio (mejor rendimiento que inRandomOrder()->first())
    //         $tratamientoId = Tratamiento::query()->pluck('id')->random();

    //         // Relacionar paciente con tratamiento
    //         $paciente->tratamientos()->attach($tratamientoId);

    //         // Obtener todas las fases asociadas al tratamiento
    //         $fases = Fase::where('trat_id', $tratamientoId)->get();

    //         // Insertar múltiples etapas en una sola consulta
    //         $etapas = $fases->map(function ($fase) use ($paciente) {
    //             return [
    //                 'name' => 'Inicio',
    //                 'paciente_id' => $paciente->id,
    //                 'fase_id' => $fase->id,
    //                 'fecha_ini' => now(),
    //                 'fecha_fin' => null,
    //                 'status' => collect(['Set Up', 'En proceso', 'Pausado', 'Finalizado'])->random(),
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];
    //         })->toArray();

    //         // Inserción masiva para mejor rendimiento
    //         Etapa::insert($etapas);
    //     });
    // }

}
