<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
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
            return back()
                ->withErrors([
                    'correo' => 'Las credenciales no coinciden con nuestros registros.',
                ])
                ->onlyInput('correo');
        }

        /** Si funciona crea una nueva sesion y lo redirige a la sesion correcta (depende del rol */
        $request->session()->regenerate();

        $idiomaActual = $request->cookie('idioma') ?? config('app.locale');

        Session::put('locale', $idiomaActual);
        Cookie::queue('idioma', $idiomaActual, 525600);

        if ($idiomaActual !== auth()->user()->idioma) {
            auth()->user()->update(['idioma' => $idiomaActual]);
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