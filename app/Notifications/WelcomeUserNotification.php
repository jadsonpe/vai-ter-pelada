<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeUserNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bem-vindo ao Vai Ter Pelada')
            ->markdown('emails.auth.welcome', [
                'user' => $notifiable,
                'dashboardUrl' => route('dashboard'),
                'peladasUrl' => route('peladas.index'),
            ]);
    }
}
