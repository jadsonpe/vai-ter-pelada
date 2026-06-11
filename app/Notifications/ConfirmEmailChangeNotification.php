<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ConfirmEmailChangeNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly User $user)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirme seu novo email - Vai Ter Pelada')
            ->markdown('emails.auth.confirm-email-change', [
                'user' => $this->user,
                'confirmUrl' => $this->confirmUrl(),
                'newEmail' => $this->user->pending_email,
            ]);
    }

    private function confirmUrl(): string
    {
        return URL::temporarySignedRoute(
            'perfil.email.confirm',
            now()->addMinutes(60),
            [
                'user' => $this->user->id,
                'hash' => sha1((string) $this->user->pending_email),
            ]
        );
    }
}
