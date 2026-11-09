<?php

namespace App\Http\Controllers\Auth;

use App\Models\UserInvitation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserInvitationController
{
    public function show(Request $request, UserInvitation $invitation, string $token): View|RedirectResponse
    {
        if (! $this->valid($invitation, $token)) {
            return redirect()->route('login')->withErrors(['correo' => __('This invitation link is invalid or has expired.')]);
        }

        if ($invitation->status === 'sent') {
            $invitation->update(['status' => 'viewed', 'viewed_at' => now()]);
        }

        return view('auth.accept-invitation', [
            'invitation' => $invitation->load('user', 'invitedBy'),
            'token' => $token,
        ]);
    }

    public function accept(Request $request, UserInvitation $invitation, string $token): RedirectResponse
    {
        if (! $this->valid($invitation, $token)) {
            return back()->withErrors(['correo' => __('This invitation link is invalid or has expired.')]);
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/'],
            'acepta_terminos' => ['accepted'],
            'acepta_privacidad' => ['accepted'],
        ], [
            'password.min' => __('Password must be at least 8 characters.'),
            'password.confirmed' => __('Passwords do not match.'),
            'password.regex' => __('Password must include an uppercase letter, a number, and a special character.'),
            'acepta_terminos.accepted' => __('You must accept the Terms and Conditions.'),
            'acepta_privacidad.accepted' => __('You must accept the Privacy Notice.'),
        ]);

        $invitation->user->forceFill([
            'contrasena' => $data['password'],
            'email_verified_at' => now(),
        ])->save();

        $invitation->forceFill([
            'status' => 'accepted',
            'accepted_at' => now(),
            'accepted_by' => $invitation->user_id,
            'accepted_ip' => $request->ip(),
            'terms_version' => 'current',
            'privacy_version' => 'current',
        ])->save();

        return redirect()->route('login')->with('status', __('Your invitation was accepted. Please log in.'));
    }

    private function valid(UserInvitation $invitation, string $token): bool
    {
        if ($invitation->expires_at->isPast() && in_array($invitation->status, ['sent', 'viewed'], true)) {
            $invitation->update(['status' => 'expired']);
        }

        return in_array($invitation->status, ['sent', 'viewed'], true)
            && now()->lessThanOrEqualTo($invitation->expires_at)
            && Hash::check($token, $invitation->token_hash);
    }
}