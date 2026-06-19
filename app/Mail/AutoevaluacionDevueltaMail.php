<?php

namespace App\Mail;

use App\Models\Autoevaluacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AutoevaluacionDevueltaMail extends Mailable
{
    use Queueable, SerializesModels;

    public Autoevaluacion $autoevaluacion;
    public string $panelUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Autoevaluacion $autoevaluacion)
    {
        $this->autoevaluacion = $autoevaluacion;
        $this->panelUrl = url('/tablero/autoevaluacions/' . $autoevaluacion->id . '/edit');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Acción Requerida: Observaciones en su Autoevaluación',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.autoevaluacion-devuelta',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
