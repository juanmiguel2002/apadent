<?php

namespace App\Jobs;

use App\Mail\RevisionReminderMail;
use App\Models\PacienteEtapas;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendReminderEmailToClinic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Fecha actual y la fecha de una semana en el futuro
        $oneWeekFromNow = Carbon::now()->addWeek();

        // Obtener etapas cuya fecha de revisión es exactamente una semana desde hoy
        $etapas = PacienteEtapas::whereDate('revision', $oneWeekFromNow->toDateString())->get();

        foreach ($etapas as $etapa) {
            $clinica = $etapa->paciente->clinica; // Asumiendo la relación clínica en el modelo de paciente
            $paciente = $etapa->paciente;

            if ($clinica) {
                // Enviar correo a la clínica
                Mail::to($clinica->email)->send(new RevisionReminderMail($clinica, $etapa, $paciente));
            }
        }
    }
}
