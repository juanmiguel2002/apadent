<?php

namespace App\Mail;

use App\Models\Paciente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecordatorioRevisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $paciente;
    public $diasRestantes;

    // Constructor para recibir los datos del paciente y los días restantes
    public function __construct(Paciente $paciente, $diasRestantes)
    {
        $this->paciente = $paciente;
        $this->diasRestantes = $diasRestantes;
    }

    public function build()
    {
        return $this->subject('Recordatorio de Revisión de Paciente')
                    ->view('emails.recordatorio_revision');
    }
}
