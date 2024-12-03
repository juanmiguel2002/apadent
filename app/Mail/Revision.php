<?php

namespace App\Mail;

use App\Models\Clinica;
use App\Models\PacienteEtapas;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RevisionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $paciente;
    public $clinica;
    public $etapa;

    /**
     * Create a new message instance.
     */
    public function __construct(Clinica $clinica, PacienteEtapas $etapa, $paciente)
    {
        $this->clinica = $clinica;
        $this->etapa = $etapa;
        $this->paciente = $paciente;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Revision Reminder Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        $url = Storage::url('storage/recursos/imagenes/logo.png');
        return $this->view('emails.reminder')
                    ->subject('Recordatorio de revisión de paciente')
                    ->with([
                        'paciente' => $this->paciente,
                        'clinica' => $this->clinica,
                        'etapa' => $this->etapa,
                        'url' => $url,
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
