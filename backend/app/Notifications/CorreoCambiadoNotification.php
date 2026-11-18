<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Aviso al correo ANTERIOR cuando el administrador cambia el correo de la cuenta (así, si fue un error
 * o alguien no autorizado, la persona se entera). Se manda con Notification::route('mail', $correoViejo).
 */
class CorreoCambiadoNotification extends Notification
{
    /** $porAdmin = false cuando la persona cambió su propio correo (el dueño, desde Configuración). */
    public function __construct(public readonly string $correoNuevo, public readonly bool $porAdmin = true) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your Nexora email was changed'))
            ->greeting(__('Hello'))
            ->line($this->porAdmin
                ? __('The administrator of your business changed the email of your Nexora account to :email.', ['email' => $this->correoNuevo])
                : __('The email of your Nexora account was changed to :email.', ['email' => $this->correoNuevo]))
            ->line($this->porAdmin
                ? __('From now on you will sign in with that email. The next time you sign in we will ask you for a code sent to it.')
                : __('From now on you will sign in with that email.'))
            ->line($this->porAdmin
                ? __('If you did not expect this change, contact your administrator.')
                : __('If you did not make this change, reset your password right away from the sign-in page.'));
    }
}
