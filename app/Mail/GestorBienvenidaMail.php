<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Reutiliza la plantilla del evaluador cambiando el rol y la liga de acceso,
 * para que ambos perfiles reciban el mismo correo institucional.
 */
class GestorBienvenidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $gestor;
    public $passwordTemporal;

    public function __construct($gestor, $passwordTemporal)
    {
        $this->gestor = $gestor;
        $this->passwordTemporal = $passwordTemporal;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido a la plataforma de Gestión +Feliz',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.evaluador_bienvenida',
            with: [
                'evaluador' => $this->gestor,
                'passwordTemporal' => $this->passwordTemporal,
                'rolLabel' => 'Gestor',
                'panelUrl' => '/gestor',
            ],
        );
    }
}
