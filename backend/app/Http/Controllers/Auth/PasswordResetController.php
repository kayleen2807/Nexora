<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showRequestForm(): View
    {
        return view('auth.password-email');
    }

    public function sendLink(Request $request): RedirectResponse
    {
        $request->merge(['correo' => strtolower(trim((string) $request->input('correo')))]);

        $request->validate([
            'correo' => ['required', 'email', 'max:150'],
        ], [
            'correo.required' => __('Email address is required.'),
            'correo.email' => __('Email address must be valid.'),
            'correo.max' => __('Email address cannot exceed 150 characters.'),
        ]);

        Password::sendResetLink($request->only('correo'));

        return back()->with('status', __('If an account uses that email, we will send a password reset link.'));
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.password-reset', [
            'token' => $token,
            'correo' => $request->query('email', ''),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'correo' => ['required', 'email', 'max:150'],
            'token' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
            ],
        ], [
            'correo.required' => __('Email address is required.'),
            'correo.email' => __('Email address must be valid.'),
            'correo.max' => __('Email address cannot exceed 150 characters.'),
            'token.required' => __('The reset link is invalid or has expired.'),
            'password.required' => __('Password is required.'),
            'password.min' => __('Password must be at least 8 characters.'),
            'password.confirmed' => __('Passwords do not match.'),
            'password.regex' => __('Password must include an uppercase letter, a number, and a special character.'),
        ]);

        $status = Password::reset(
            [
                'correo' => $validated['correo'],
                'token' => $validated['token'],
                'password' => $validated['password'],
                'password_confirmation' => $request->input('password_confirmation'),
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'contrasena' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __('Your password has been reset. Please log in.'));
        }

        $message = match ($status) {
            Password::RESET_THROTTLED => __('Please wait before requesting another reset link.'),
            default => __('The reset link is invalid or has expired.'),
        };

        return back()->withErrors(['correo' => $message]);
    }
}