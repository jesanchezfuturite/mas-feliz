<?php

namespace App\Mail;

use App\Models\Empresa;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccesosTableroEmpresa extends Mailable
{
    use Queueable, SerializesModels;

    public Empresa $empresa;
    public string $passwordTemporal;

    /**
     * Create a new message instance.
     */
    public function __construct(Empresa $empresa, string $passwordTemporal)
    {
        $this->empresa = $empresa;
        $this->passwordTemporal = $passwordTemporal;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Accesos al Tablero +Feliz - Folio ' . $this->empresa->folio,
            bcc: [
                'enrique.sanchez@futurite.com',
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.accesos',
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
