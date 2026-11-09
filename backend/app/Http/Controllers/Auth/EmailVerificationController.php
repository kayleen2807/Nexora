<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class EmailVerificationController
{
    /** Códigos incorrectos permitidos antes de anular el código. */
    private const MAX_INTENTOS_CODIGO = 5;

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

        $llave = self::llaveIntentos($user->correo);

        if (! $user->email_verification_code || ! Hash::check($data['codigo'], $user->email_verification_code)) {
            RateLimiter::hit($llave, 600); // dura lo mismo que el código (10 min)

            // Anti fuerza bruta: al 5º código incorrecto se anula y hay que pedir uno nuevo
            if (RateLimiter::tooManyAttempts($llave, self::MAX_INTENTOS_CODIGO)) {
                $user->forceFill(['email_verification_code' => null, 'email_verification_expires_at' => null])->save();
                RateLimiter::clear($llave);

                return back()->withErrors(['codigo' => __('Too many incorrect codes. Request a new code.')]);
            }

            return back()->withErrors(['codigo' => __('The verification code is incorrect.')]);
        }

        RateLimiter::clear($llave);

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

        $correo = strtolower(trim($data['correo']));

        // Anti spam: 1 reenvío por minuto y 5 por hora para el mismo correo. Se aplica
        // aunque la cuenta no exista, para no revelar qué correos están registrados.
        foreach ([['reenvio-min', 1, 60], ['reenvio-hora', 5, 3600]] as [$tipo, $max, $segundos]) {
            $llave = "$tipo|$correo";
            if (RateLimiter::tooManyAttempts($llave, $max)) {
                return back()->withErrors(['correo' => __('Please wait :seconds seconds before requesting another code.', ['seconds' => RateLimiter::availableIn($llave)])]);
            }
        }
        RateLimiter::hit("reenvio-min|$correo", 60);
        RateLimiter::hit("reenvio-hora|$correo", 3600);

        $user = User::where('correo', $correo)->first();

        if ($user && ! $user->email_verified_at) {
            $service->send($user);
            RateLimiter::clear(self::llaveIntentos($correo)); // código nuevo = intentos desde cero
        }

        return back()->with('status', __('If the account exists and is not verified, a new code was sent.'));
    }

    private static function llaveIntentos(string $correo): string
    {
        return 'verificar|'.strtolower($correo);
    }
}