<?php

namespace App\Mail;

use App\Models\Clinica;
use App\Models\Factura;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevaFactura extends Mailable
{
    use Queueable, SerializesModels;

    public $clinica, $factura;
    /**
     * Create a new message instance.
     */
    public function __construct(Clinica $clinica, Factura $factura)
    {
        $this->clinica = $clinica;
        $this->factura = $factura;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva Factura',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->subject('Nueva Factura')
            ->view('emails.nuevaFactura')
            ->with([
                'name' => $this->clinica->name,
                'nameFactura' => $this->factura->name,
                'fecha' => $this->factura->created_at->format('d/m/Y'),
                // 'usuario' => $this->factura->user(),
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
