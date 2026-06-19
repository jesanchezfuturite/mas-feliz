<?php

namespace App\Mail;

use App\Models\Autoevaluacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DistintivoAprobadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public Autoevaluacion $autoevaluacion;
    public string $nivelMadurez;
    public string $dictamenFinal;
    public string $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(Autoevaluacion $autoevaluacion, string $nivelMadurez, string $dictamenFinal, string $pdfPath)
    {
        $this->autoevaluacion = $autoevaluacion;
        $this->nivelMadurez = $nivelMadurez;
        $this->dictamenFinal = $dictamenFinal;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Felicidades! Su Distintivo +Feliz ha sido Aprobado',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.distintivo-aprobado',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('public', $this->pdfPath)
                ->as('Distintivo_Mas_Feliz_' . $this->autoevaluacion->empresa->folio . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
