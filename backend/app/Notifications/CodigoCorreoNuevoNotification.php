<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Código para confirmar el correo NUEVO cuando el dueño cambia el suyo (mismo diseño que el de dos pasos). */
class CodigoCorreoNuevoNotification extends Notification
{
    public const MINUTOS_VIGENCIA = 10;

    public function __construct(public readonly string $code) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Confirm your new Nexora email'))
            ->view([
                'html' => 'emails.dos-pasos-codigo',
                'text' => 'emails.dos-pasos-codigo-text',
            ], [
                'logoUrl' => asset('img/nexora-logo-cropped.png'),
                'code' => $this->code,
                'intro' => __('Use this code to confirm your new email in Nexora:'),
                'expires' => self::MINUTOS_VIGENCIA,
            ]);
    }
}
