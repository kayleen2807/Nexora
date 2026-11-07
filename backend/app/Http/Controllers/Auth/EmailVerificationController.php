<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmailVerificationController
{
    public function show(Request $request): View
    {
        return view('auth.verify-email', [
            'correo' => $request->query('correo', $request->user()?->correo ?? ''),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'correo' => ['required', 'email', 'max:150'],
            'codigo' => ['required', 'digits:6'],
        ], [
            'correo.required' => __('Email address is required.'),
            'correo.email' => __('Email address must be valid.'),
            'codigo.required' => __('Verification code is required.'),
            'codigo.digits' => __('The verification code must have 6 digits.'),
        ]);

        $user = User::where('correo', strtolower(trim($data['correo'])))->first();

        if (! $user || $user->email_verified_at) {
            return back()->withErrors(['codigo' => __('This verification request is invalid.')]);
        }

        if (! $user->email_verification_expires_at || now()->greaterThan($user->email_verification_expires_at)) {
            return back()->withErrors(['codigo' => __('The verification code has expired.')]);
        }

        if (! $user->email_verification_code || ! Hash::check($data['codigo'], $user->email_verification_code)) {
            return back()->withErrors(['codigo' => __('The verification code is incorrect.')]);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
        ])->save();

        if ($request->user()?->is($user)) {
            return redirect()->route('dashboard')->with('status', __('Your email has been verified.'));
        }

        return redirect()->route('login')->with('status', __('Your email has been verified. Please log in.'));
    }

    public function resend(Request $request, EmailVerificationService $service): RedirectResponse
    {
        $data = $request->validate([
            'correo' => ['required', 'email', 'max:150'],
        ]);

        $user = User::where('correo', strtolower(trim($data['correo'])))->first();

        if ($user && ! $user->email_verified_at) {
            $service->send($user);
        }

        return back()->with('status', __('If the account exists and is not verified, a new code was sent.'));
    }
}