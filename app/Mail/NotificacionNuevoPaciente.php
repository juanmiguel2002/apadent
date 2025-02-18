<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionNuevoPaciente extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $clinica;
    public $paciente;
    public $perfilPacienteUrl;

    public function __construct($user, $paciente, $clinica = null, $perfilPacienteUrl = null)
    {
        $this->nombre = $user->name;
        $this->clinica = $clinica;
        $this->paciente = $paciente;
        $this->perfilPacienteUrl = $perfilPacienteUrl;
    }

    public function build()
    {
        return $this->markdown('emails.nuevoPaciente')
                    ->with([
                        'nombre' => $this->nombre,
                        'clinicaName' => $this->clinica,
                        'perfilPacienteUrl' => $this->perfilPacienteUrl,
                        'paciente' => $this->paciente,
                        'fechaRegistro' => now()->format('d/m/Y H:i'),
                    ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
