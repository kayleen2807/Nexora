<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserInvitation;
use App\Notifications\UserInvitationNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserInvitationService
{
    public function send(User $user, User $invitedBy): UserInvitation
    {
        UserInvitation::where('user_id', $user->id_usuario)
            ->whereIn('status', ['sent', 'viewed'])
            ->update(['status' => 'revoked']);

        $token = Str::random(64);
        $invitation = UserInvitation::create([
            'user_id' => $user->id_usuario,
            'invited_by' => $invitedBy->id_usuario,
            'token_hash' => Hash::make($token),
            'expires_at' => now()->addMinutes(5),
            'sent_at' => now(),
            'status' => 'sent',
        ]);

        $user->notify(new UserInvitationNotification($invitation, $token, $invitedBy));

        return $invitation;
    }
}