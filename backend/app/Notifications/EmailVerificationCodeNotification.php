<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationCodeNotification extends Notification
{
    public function __construct(public readonly string $code)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Verify your Nexora email'))
            ->view([
                'html' => 'emails.email-verification-code',
                'text' => 'emails.email-verification-code-text',
            ], [
                'logoUrl' => asset('img/nexora-logo-cropped.png'),
                'code' => $this->code,
                'expires' => 10,
                'termsUrl' => route('legal.terminos'),
                'privacyUrl' => route('legal.privacidad'),
            ]);
    }
}