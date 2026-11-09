<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    /** Intentos fallidos permitidos por minuto (por correo + IP). */
    private const MAX_INTENTOS = 5;

    /**
     * Mostrar el formulario de inicio de sesión.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Autenticar al usuario contra la tabla "usuario".
     */
    public function login(Request $request): RedirectResponse
    {
        /**
         * Credenciales requeridos para el login (usuario y contra) y se compruebam si estan vacios
         */
        $credentials = $request->validate([
            'correo' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        /**
         * Límite anti fuerza bruta: máximo 5 intentos fallidos por minuto para la
         * misma combinación correo + IP. Al pasarse, ni la contraseña correcta entra
         * hasta que termine la espera.
         */
        $llave = 'login|'.strtolower(trim($credentials['correo'])).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($llave, self::MAX_INTENTOS)) {
            return back()
                ->withErrors(['correo' => __('Too many login attempts. Try again in :seconds seconds.', ['seconds' => RateLimiter::availableIn($llave)])])
                ->onlyInput('correo');
        }

        /**
         * Autentica las credenciales con el correo guardada en la bd
         */

        $authenticated = Auth::attempt(
            [
                'correo' => $credentials['correo'],
                'password' => $credentials['password'],
            ],
            /**Boton de recuerdame para evitar pedir las credenciales cada vez (se guarda el token) */
            $request->boolean('remember')
        );

        /** Si las credenciales no coiciden muestra un mensaje de error y regresa al login*/
        if (! $authenticated) {
            RateLimiter::hit($llave, 60); // el contador se borra solo a los 60 segundos

            return back()
                ->withErrors([
                    'correo' => 'Las credenciales no coinciden con nuestros registros.',
                ])
                ->onlyInput('correo');
        }

        // Credenciales correctas: se reinicia el contador de intentos
        RateLimiter::clear($llave);

        /** Las cuentas nuevas deben confirmar su correo antes de entrar al sistema. */
        $usuario = Auth::user();
        if (! $usuario->email_verified_at) {
            Auth::logout();

            return redirect()->route('verification.notice', ['correo' => $usuario->correo])
                ->withErrors(['correo' => __('Please verify your email before logging in.')]);
        }

        /** Si funciona crea una nueva sesion y lo redirige a la sesion correcta (depende del rol */
        $request->session()->regenerate();

        $idiomaActual = $request->cookie('idioma') ?? config('app.locale');

        Session::put('locale', $idiomaActual);
        Cookie::queue('idioma', $idiomaActual, 525600);

        /** @var User $usuario */
        $usuario = Auth::user();

        if ($idiomaActual !== $usuario->idioma) {
            $usuario->update(['idioma' => $idiomaActual]);
        }

        return redirect()->route('dashboard');
    }

    /**
     * Cerrar la sesión del usuario.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        /** Desues de eliminar la sesion actual, se regenera un token nuevo */
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}