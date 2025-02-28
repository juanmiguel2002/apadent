<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionMensaje extends Mailable
{
    use Queueable, SerializesModels;

    public $paciente, $etapa, $mensaje, $trat;
    /**
     * Create a new message instance.
     */
    public function __construct($paciente, $etapa, $trat, $mensaje)
    {
        $this->paciente = $paciente;
        $this->etapa = $etapa;
        $this->mensaje = $mensaje;
        $this->trat = $trat;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo Mensaje',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {

        return $this->subject('Nuevo Mensaje')
            ->view('emails.mensaje');
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
