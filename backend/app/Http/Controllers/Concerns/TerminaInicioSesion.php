<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use App\Services\DosPasosService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

/**
 * Los últimos pasos de un inicio de sesión, compartidos por LoginController (correo + contraseña),
 * GoogleController y DosPasosController (pantalla del código), para que los tres terminen igual.
 */
trait TerminaInicioSesion
{
    /** Llave de la sesión donde espera el usuario que ya pasó la contraseña pero aún no el código. */
    protected const SESION_DOS_PASOS = 'dos_pasos_pendiente';

    /**
     * Primer paso correcto (contraseña o Google) y la cuenta tiene la verificación en dos pasos:
     * todavía NO entra. Se recuerda quién es en la sesión, se le manda el código y se le muestra la pantalla.
     *
     * @param  string  $origen  'login' | 'google' (cada uno termina con su propio manejo de idioma)
     */
    protected function pedirSegundoPaso(Request $request, User $usuario, bool $recordar, string $origen, DosPasosService $dosPasos): RedirectResponse
    {
        Auth::logout();                      // si Auth::attempt ya lo había dejado entrar, se le saca
        $request->session()->regenerate();   // id de sesión nuevo: evita fijación de sesión

        $request->session()->put(self::SESION_DOS_PASOS, [
            'id' => $usuario->id_usuario,
            'recordar' => $recordar,
            'origen' => $origen,
            'desde' => now()->timestamp,
        ]);

        // Si ya tiene un código vigente (por ejemplo, volvió a escribir la contraseña) ese sigue sirviendo.
        // Así, iniciar sesión muchas veces no llena su correo: máximo 1 código cada 10 minutos por esta vía;
        // el límite de 1 por minuto / 5 por hora es para el botón "Enviar un código nuevo".
        if (! $dosPasos->tieneCodigoVigente($usuario)) {
            $dosPasos->enviarCodigo($usuario, 'login');
        }

        return redirect()->route('dos-pasos.show');
    }

    /** Último paso: sesión nueva, idioma y panel según el rol. */
    protected function terminarInicioSesion(Request $request, User $usuario, string $origen): RedirectResponse
    {
        $request->session()->regenerate();

        if ($origen === 'google') {
            // Mismo manejo de idioma que tenía GoogleController
            $idiomaActual = $request->cookie('idioma') ?? $usuario->idioma ?? config('app.locale');
            Session::put('locale', $idiomaActual);
            Cookie::queue('idioma', $idiomaActual, 525600);

            return redirect()->intended(route('dashboard'));
        }

        // Mismo manejo de idioma que tenía LoginController: la cookie gana y se escribe en la BD
        $idiomaActual = $request->cookie('idioma') ?? config('app.locale');
        Session::put('locale', $idiomaActual);
        Cookie::queue('idioma', $idiomaActual, 525600);

        if ($idiomaActual !== $usuario->idioma) {
            $usuario->update(['idioma' => $idiomaActual]);
        }

        return redirect()->route('dashboard');
    }
}
