<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetUsuarioPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $baseUrl = rtrim((string) env('FRONTEND_RESET_PASSWORD_URL', config('app.url').'/reset-password'), '/');
        $url = $baseUrl.'?token='.$this->token.'&correo='.urlencode((string) $notifiable->correo);

        return (new MailMessage)
            ->subject('Recuperacion de contrasena')
            ->line('Recibimos una solicitud para restablecer tu contrasena.')
            ->action('Restablecer contrasena', $url)
            ->line('Si no solicitaste este cambio, puedes ignorar este correo.');
    }
}
