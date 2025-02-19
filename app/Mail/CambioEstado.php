<?php

namespace App\Mail;

use App\Models\Paciente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CambioEstado extends Mailable
{
    use Queueable, SerializesModels;

    public $paciente, $estado, $etapa, $trat, $clinica;

    /**
     * Create a new message instance.
     */
    public function __construct(Paciente $paciente, $estado, $etapa, $trat, $clinica)
    {
        $this->paciente = $paciente;
        $this->estado = $estado;
        $this->etapa = $etapa;
        $this->trat = $trat;
        $this->clinica = $clinica;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cambio Estado',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->view('emails.cambioEstado')
                    ->with([
                        'paciente' => $this->paciente,
                        'estado' => $this->estado,
                        'etapa' => $this->etapa,
                        'trat' => $this->trat,
                        'clinica' => $this->clinica,
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
