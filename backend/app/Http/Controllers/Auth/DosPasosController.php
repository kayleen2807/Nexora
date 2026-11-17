<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Concerns\TerminaInicioSesion;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DosPasosService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Pantalla del segundo paso del inicio de sesión: el usuario ya pasó la contraseña (o Google)
 * y escribe el código que le llegó al correo. Mientras tanto NO tiene sesión iniciada: solo
 * la sesión recuerda quién es (TerminaInicioSesion::SESION_DOS_PASOS).
 */
class DosPasosController extends Controller
{
    use TerminaInicioSesion;

    /** Tiempo máximo entre la contraseña y el código; después hay que volver a escribir la contraseña. */
    private const MINUTOS_MAXIMOS = 15;

    public function show(Request $request): View|RedirectResponse
    {
        $usuario = $this->usuarioPendiente($request);
        if (! $usuario) {
            return $this->volverAlLogin($request);
        }

        return view('auth.dos-pasos', ['correo' => self::ocultarCorreo($usuario->correo)]);
    }

    public function verificar(Request $request, DosPasosService $dosPasos): RedirectResponse
    {
        $usuario = $this->usuarioPendiente($request);
        if (! $usuario) {
            return $this->volverAlLogin($request);
        }

        $data = $request->validate([
            'codigo' => ['required', 'digits:6'],
        ], [
            'codigo.required' => __('Verification code is required.'),
            'codigo.digits' => __('The verification code must have 6 digits.'),
        ]);

        $error = $dosPasos->verificar($usuario, $data['codigo']);
        if ($error) {
            return back()->withErrors(['codigo' => $error]);
        }

        // Código correcto: ahora sí entra (con "Recuérdame" si lo había marcado)
        $pendiente = $request->session()->pull(self::SESION_DOS_PASOS);
        Auth::login($usuario, (bool) $pendiente['recordar']);

        return $this->terminarInicioSesion($request, $usuario, $pendiente['origen']);
    }

    public function reenviar(Request $request, DosPasosService $dosPasos): RedirectResponse
    {
        $usuario = $this->usuarioPendiente($request);
        if (! $usuario) {
            return $this->volverAlLogin($request);
        }

        $espera = $dosPasos->esperaParaEnviar($usuario);
        if ($espera > 0) {
            return back()->withErrors(['codigo' => __('Please wait :seconds seconds before requesting another code.', ['seconds' => $espera])]);
        }

        $dosPasos->enviarCodigo($usuario);

        return back()->with('status', __('We sent you a new code.'));
    }

    /** El usuario que espera el código, o null si no hay ninguno o ya pasó demasiado tiempo. */
    private function usuarioPendiente(Request $request): ?User
    {
        $pendiente = $request->session()->get(self::SESION_DOS_PASOS);

        if (! $pendiente || now()->timestamp - $pendiente['desde'] > self::MINUTOS_MAXIMOS * 60) {
            return null;
        }

        $usuario = User::find($pendiente['id']);

        // Si mientras tanto desactivó la verificación (o se borró la cuenta), se empieza de nuevo
        return $usuario?->dos_pasos_activo ? $usuario : null;
    }

    private function volverAlLogin(Request $request): RedirectResponse
    {
        $request->session()->forget(self::SESION_DOS_PASOS);

        return redirect()->route('login')->withErrors(['correo' => __('Your security code session expired. Please log in again.')]);
    }

    /** kavendano@ucol.mx → ka*******@ucol.mx (se muestra a dónde llegó el código sin revelarlo completo). */
    private static function ocultarCorreo(string $correo): string
    {
        [$nombre, $dominio] = array_pad(explode('@', $correo, 2), 2, '');

        return mb_substr($nombre, 0, 2).str_repeat('*', max(mb_strlen($nombre) - 2, 3)).'@'.$dominio;
    }
}
