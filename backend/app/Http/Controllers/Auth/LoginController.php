<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
            'usuario' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        /**
         * Autentica las credenciales con el correo guardada en la bd
         */

        $authenticated = Auth::attempt(
            [
                'correo' => $credentials['usuario'],
                'password' => $credentials['password'],
            ],
            /**Boton de recuerdame para evitar pedir las credenciales cada vez (se guarda el token) */
            $request->boolean('remember')
        );

        /** Si las credenciales no coiciden muestra un mensaje de error y regresa al login*/
        if (! $authenticated) {
            return back()
                ->withErrors([
                    'usuario' => 'Las credenciales no coinciden con nuestros registros.',
                ])
                ->onlyInput('usuario');
        }

        /** Si funciona crea una nueva sesion y lo redirige a la sesion correcta (depende del rol */
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
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