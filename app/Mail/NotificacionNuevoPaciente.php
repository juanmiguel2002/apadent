<?php

namespace App\Mail;

use App\Models\Paciente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NotificacionNuevoPaciente extends Mailable
{
    use Queueable, SerializesModels;

    public $paciente;

    /**
     * Create a new message instance.
     */
    public function __construct(Paciente $paciente)
    {
        $this->paciente = $paciente;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificacion Nuevo Paciente',

        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->subject('Nuevo Paciente Creado')
            ->view('emails.nuevoPaciente')
            ->with([
                'pacienteName' => $this->paciente->name,
                'pacienteApellidos' => $this->paciente->apellidos,
                'pacienteEmail' => $this->paciente->email,
                'pacienteTelefono' => $this->paciente->telefono,
                'pacienteFechaNacimiento' => date('d/m/Y', strtotime($this->paciente->fecha_nacimiento)),
                'fechaRegistro' => now()->format('d/m/Y H:i'),
                // 'clinica' => Auth::user()->clinicas,
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
