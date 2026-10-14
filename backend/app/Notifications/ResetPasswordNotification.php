<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject(__('Reset your password'))
            ->view([
                'html' => 'emails.password-reset',
                'text' => 'emails.password-reset-text',
            ], [
                'logoUrl' => asset('img/nexora-logo-cropped.png'),
                'url' => $url,
                'expires' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire'),
            ]);
    }
}