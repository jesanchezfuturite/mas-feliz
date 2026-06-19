<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseNotification;

class ResetPasswordNotificationEs extends BaseNotification
{
    public string $url;

    /**
     * Create a new notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct(string $token)
    {
        parent::__construct($token);
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Use the manually set url if available (Filament style), otherwise generate it (Laravel style)
        $resetUrl = isset($this->url) ? $this->url : $this->resetUrl($notifiable);

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Restablecer Contraseña - Plataforma Más Feliz')
            ->view('emails.recuperar-password', [
                'url' => $resetUrl,
                'notifiable' => $notifiable,
            ]);
    }
}
