<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionRevision extends Mailable
{
    use Queueable, SerializesModels;

    public $paciente, $etapa, $clinica;

    /**
     * Create a new message instance.
     */
    public function __construct($paciente, $etapa, $clinica)
    {
        $this->paciente = $paciente;
        $this->etapa = $etapa;
        $this->clinica = $clinica;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificacion Revision',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->subject('Nueva Factura')
            ->view('emails.reminder');
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
