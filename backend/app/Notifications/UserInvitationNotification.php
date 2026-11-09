<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\UserInvitation;
use App\Services\UserInvitationService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitationNotification extends Notification
{
    public function __construct(
        public readonly UserInvitation $invitation,
        public readonly string $token,
        public readonly User $invitedBy,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('invitation.accept', [
            'invitation' => $this->invitation->id,
            'token' => $this->token,
        ]);

        return (new MailMessage)
            ->subject(__('You have been invited to Nexora'))
            ->view([
                'html' => 'emails.user-invitation',
                'text' => 'emails.user-invitation-text',
            ], [
                'logoUrl' => asset('img/nexora-logo-cropped.png'),
                'url' => $url,
                'user' => $notifiable,
                'invitedBy' => $this->invitedBy,
                'branch' => $notifiable->sucursal?->nombre ?? __('Not assigned'),
                'role' => ucfirst($notifiable->rol?->nombre ?? __('User')),
                'registrationDate' => $this->invitation->created_at,
                'expires' => UserInvitationService::HORAS_VIGENCIA,
                'termsUrl' => route('legal.terminos'),
                'privacyUrl' => route('legal.privacidad'),
            ]);
    }
}