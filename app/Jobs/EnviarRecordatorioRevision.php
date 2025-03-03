<?php

namespace App\Jobs;

use App\Mail\RecordatorioRevisionMail;
use App\Models\Clinica;
use App\Models\Etapa;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnviarRecordatorioRevision implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pacienteId;

    // Constructor para recibir el ID del paciente
    public function __construct($pacienteId)
    {
        $this->pacienteId = $pacienteId;
    }

    public function handle()
    {
        // Obtener el paciente
        $paciente = Paciente::findOrFail($this->pacienteId);
        $clinica = Clinica::find($paciente->clinica_id);
        // Obtener la última etapa del paciente (asumimos que la próxima revisión es la última)
        $etapa = Etapa::where('paciente_id', $this->pacienteId)
                      ->orderBy('revision', 'desc')
                      ->first();

        if ($etapa) {
            // Calcular la diferencia de días entre hoy y la fecha de la revisión
            $diasRestantes = Carbon::parse($etapa->revision)->diffInDays(Carbon::now());

            // Verificar si la revisión es dentro de los próximos 7 días
            if ($diasRestantes <= 7) {
                // Enviar el correo a la clínica (suponiendo que tienes la dirección de la clínica)
                Mail::to($clinica->email)->send(new RecordatorioRevisionMail($paciente, $diasRestantes));
            }
        }
    }
}
