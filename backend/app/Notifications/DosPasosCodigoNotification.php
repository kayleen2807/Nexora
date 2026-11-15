<?php

namespace App\Notifications;

use App\Services\DosPasosService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Correo con el código de la verificación en dos pasos (mismo diseño que el de verificar correo). */
class DosPasosCodigoNotification extends Notification
{
    /** @param  string  $motivo  'login' | 'activar' | 'desactivar' */
    public function __construct(public readonly string $code, public readonly string $motivo = 'login') {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $intro = match ($this->motivo) {
            'activar' => __('Use this code to turn on two-step verification:'),
            'desactivar' => __('Use this code to turn off two-step verification:'),
            default => __('Use this code to finish signing in to Nexora:'),
        };

        return (new MailMessage)
            ->subject(__('Your Nexora security code'))
            ->view([
                'html' => 'emails.dos-pasos-codigo',
                'text' => 'emails.dos-pasos-codigo-text',
            ], [
                'logoUrl' => asset('img/nexora-logo-cropped.png'),
                'code' => $this->code,
                'intro' => $intro,
                'expires' => DosPasosService::MINUTOS_VIGENCIA,
            ]);
    }
}
