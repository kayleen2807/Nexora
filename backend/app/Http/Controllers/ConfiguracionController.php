<?php

/*
 * EJEMPLO para adaptar. Atiende la sección "Configuración" de los tres paneles (cajero, gerente y administrador).
 *
 * IDEA CLAVE: nunca se manda el correo desde la pantalla. El usuario que cambia su nombre o su contraseña es
 * el que inició sesión (con su correo), y se obtiene del servidor con $request->user(). Así nadie puede cambiar
 * la cuenta de otra persona, ni manipulando la petición.
 */

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\CodigoCorreoNuevoNotification;
use App\Notifications\CorreoCambiadoNotification;
use App\Services\DosPasosService;
use App\Services\EliminacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ConfiguracionController extends Controller
{
    /** Columna donde se guarda la contraseña encriptada (en la tabla usuario se llama "contrasena"). */
    private const COLUMNA_PASSWORD = 'contrasena';

    /* GET /configuracion/perfil — lo que muestra la pantalla */
    public function perfil(Request $request)
    {
        $usuario = $request->user();

        return response()->json([
            'nombre'   => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'correo'   => $usuario->correo,                 // solo se muestra; no se puede editar aquí
            'rol'      => optional($usuario->rol)->nombre,  // ajusta a tu relación de rol
            'sucursal' => optional($usuario->sucursal)->nombre,
            'dos_pasos' => (bool) $usuario->dos_pasos_activo,  // estado real del interruptor "Verificación en dos pasos"
        ]);
    }

    /*
     * PUT /configuracion/dos-pasos {activar: bool} — el interruptor de Configuración (decisión de Kay):
     * activar y desactivar son inmediatos, sin código. Una vez activa, el código se pide en CADA inicio de
     * sesión (LoginController / GoogleController → TerminaInicioSesion::pedirSegundoPaso).
     */
    public function cambiarDosPasos(Request $request, DosPasosService $dosPasos)
    {
        $data = $request->validate(['activar' => ['required', 'boolean']]);
        $usuario = $request->user();

        $dosPasos->cambiarEstado($usuario, (bool) $data['activar']);

        return response()->json(['dos_pasos' => (bool) $usuario->dos_pasos_activo]);
    }

    /* PUT /configuracion/perfil — guarda nombre y apellido del usuario con sesión iniciada */
    public function actualizarPerfil(Request $request)
    {
        $datos = $request->validate([
            'nombre'   => ['required', 'string', 'max:80'],
            'apellido' => ['nullable', 'string', 'max:80'],
        ], [
            'nombre.required' => 'Escribe tu nombre.',
            'nombre.max'      => 'El nombre puede tener máximo 80 caracteres.',
            'apellido.max'    => 'El apellido puede tener máximo 80 caracteres.',
        ]);

        $usuario = $request->user();
        $usuario->nombre = $datos['nombre'];
        $usuario->apellido = $datos['apellido'] ?? '';
        $usuario->save();            // el correo no se toca

        return response()->json([
            'nombre'   => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'correo'   => $usuario->correo,
        ]);
    }

    /* PUT /configuracion/password — cambia la contraseña del usuario con sesión iniciada */
    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password_actual' => ['required', 'string'],
            // "confirmed" busca el campo password_nueva_confirmation, que es el que manda la pantalla
            // Mismas reglas que el registro: 8+ caracteres, una mayúscula, un número y un carácter especial
            'password_nueva'  => ['required', 'string', 'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', 'confirmed', 'different:password_actual'],
        ], [
            'password_actual.required' => 'Escribe tu contraseña actual.',
            'password_nueva.required'  => 'Escribe la nueva contraseña.',
            'password_nueva.regex'     => 'La contraseña debe tener mínimo 8 caracteres, una mayúscula, un número y un carácter especial.',
            'password_nueva.confirmed' => 'Las contraseñas no coinciden.',
            'password_nueva.different' => 'La nueva contraseña debe ser distinta a la actual.',
        ]);

        $usuario = $request->user();

        // 1) La contraseña actual tiene que ser correcta (se compara contra la encriptada guardada).
        //    Las cuentas creadas con Google tienen una contraseña aleatoria que nadie conoce (la columna es
        //    NOT NULL): para tener una propia usan "¿Olvidaste tu contraseña?" en el inicio de sesión.
        if (!Hash::check($request->password_actual, $usuario->{self::COLUMNA_PASSWORD})) {
            $mensaje = 'La contraseña actual no es correcta. Si entras con Google, crea una con "¿Olvidaste tu contraseña?" en el inicio de sesión.';

            return response()->json([
                'message' => $mensaje,
                'errors'  => ['password_actual' => [$mensaje]],
            ], 422);
        }

        // 2) Se guarda ENCRIPTADA: el modelo User tiene el cast 'contrasena' => 'hashed', que la encripta
        //    sola al asignarla. Por eso NO se usa Hash::make() aquí (se encriptaría dos veces y ya no
        //    se podría iniciar sesión).
        $usuario->{self::COLUMNA_PASSWORD} = $request->password_nueva;
        $usuario->save();

        // 3) Opcional: cierra la sesión en los demás dispositivos (requiere el middleware
        //    \Illuminate\Session\Middleware\AuthenticateSession en el grupo 'web')
        // Auth::logoutOtherDevices($request->password_nueva);

        return response()->json(['ok' => true]);
    }

    /*
     * Cambio del correo del DUEÑO (los empleados se lo piden al administrador). Paso 1:
     * POST /configuracion/correo {correo, password} → manda un código de 6 dígitos al correo NUEVO.
     * El cambio pendiente vive 10 min en la caché (sin columnas nuevas); el correo aún no cambia.
     */
    public function solicitarCambioCorreo(Request $request)
    {
        /** @var User $usuario */
        $usuario = $request->user();
        abort_unless($this->esDueno($usuario), 403, 'Tu correo lo cambia el administrador de tu negocio.');

        $request->merge(['correo' => strtolower(trim((string) $request->input('correo')))]);
        $datos = $request->validate([
            'correo' => ['required', 'email', 'max:150', Rule::unique('usuario', 'correo')->ignore($usuario->id_usuario, 'id_usuario')],
            'password' => ['required', 'string'],
        ], [
            'correo.required' => 'Escribe el correo nuevo.',
            'correo.email' => 'El correo no es válido.',
            'correo.unique' => 'Ese correo ya está registrado.',
            'password.required' => 'Escribe tu contraseña para confirmar.',
        ]);
        abort_if($datos['correo'] === $usuario->correo, 422, 'Ese ya es tu correo.');
        if (! Hash::check($datos['password'], $usuario->{self::COLUMNA_PASSWORD})) {
            $mensaje = 'La contraseña no es correcta. Si entras con Google, crea una con "¿Olvidaste tu contraseña?" en el inicio de sesión.';

            return response()->json(['message' => $mensaje, 'errors' => ['password' => [$mensaje]]], 422);
        }

        // Máximo un código por minuto (no se puede usar para llenar de correos a alguien)
        $llave = 'cambio-correo-envio|'.$usuario->id_usuario;
        abort_if(RateLimiter::tooManyAttempts($llave, 1), 429, 'Espera '.RateLimiter::availableIn($llave).' segundos para pedir otro código.');

        $codigo = (string) random_int(100000, 999999);
        try {
            Notification::route('mail', $datos['correo'])
                ->notify((new CodigoCorreoNuevoNotification($codigo))->locale($usuario->idioma ?: 'es'));
        } catch (TransportExceptionInterface $e) {
            report($e);

            return response()->json(['message' => 'No pudimos enviar el código a '.$datos['correo'].'. Revisa que esté bien escrito e inténtalo de nuevo.'], 503);
        }
        RateLimiter::hit($llave, 60);
        Cache::put($this->llaveCambioCorreo($usuario), [
            'correo' => $datos['correo'],
            'codigo' => Hash::make($codigo),
            'intentos' => 0,
        ], now()->addMinutes(CodigoCorreoNuevoNotification::MINUTOS_VIGENCIA));

        return response()->json(['message' => 'Te enviamos un código a '.$datos['correo'].'.', 'correo' => $datos['correo']]);
    }

    /* Paso 2: POST /configuracion/correo/confirmar {codigo} → si es correcto, cambia el correo y avisa al anterior. */
    public function confirmarCambioCorreo(Request $request)
    {
        /** @var User $usuario */
        $usuario = $request->user();
        abort_unless($this->esDueno($usuario), 403);
        $request->validate(['codigo' => ['required', 'digits:6']], ['codigo.required' => 'Escribe el código.', 'codigo.digits' => 'El código tiene 6 dígitos.']);

        $llave = $this->llaveCambioCorreo($usuario);
        $pendiente = Cache::get($llave);
        abort_if(! $pendiente, 422, 'El código venció o no has pedido uno. Vuelve a escribir el correo nuevo.');

        if (! Hash::check($request->codigo, $pendiente['codigo'])) {
            $pendiente['intentos']++;
            // 5 intentos fallidos anulan el código (igual que la verificación del registro y los dos pasos)
            $pendiente['intentos'] >= 5 ? Cache::forget($llave) : Cache::put($llave, $pendiente, now()->addMinutes(CodigoCorreoNuevoNotification::MINUTOS_VIGENCIA));
            abort(422, $pendiente['intentos'] >= 5 ? 'Demasiados intentos. Pide un código nuevo.' : 'El código no es correcto.');
        }

        // Alguien pudo registrar ese correo mientras tanto
        abort_if(User::where('correo', $pendiente['correo'])->where('id_usuario', '!=', $usuario->id_usuario)->exists(), 422, 'Ese correo ya está registrado.');

        $correoViejo = $usuario->correo;
        $usuario->forceFill(['correo' => $pendiente['correo'], 'email_verified_at' => now()])->save();
        Cache::forget($llave);
        DB::table('password_reset_tokens')->where('email', $correoViejo)->delete();

        try {   // el aviso al correo anterior es informativo: si no sale, el cambio ya está hecho
            Notification::route('mail', $correoViejo)
                ->notify((new CorreoCambiadoNotification($usuario->correo, false))->locale($usuario->idioma ?: 'es'));
        } catch (TransportExceptionInterface $e) {
            report($e);
        }

        return response()->json(['correo' => $usuario->correo]);
    }

    private function esDueno(User $usuario): bool
    {
        return strtolower($usuario->rol->nombre ?? '') === 'administrador';
    }

    private function llaveCambioCorreo(User $usuario): string
    {
        return 'cambio-correo|'.$usuario->id_usuario;
    }

    /*
     * DELETE /configuracion/cuenta {password, confirmacion?} — "Eliminar mi cuenta" (decisión de Kay 2026-11-17).
     * Gerente/cajero: se borra, o se anonimiza si tiene ventas/cortes (EliminacionService). Dueño (admin):
     * se borran TODOS sus negocios; además de la contraseña debe escribir su correo para confirmar.
     */
    public function eliminarCuenta(Request $request, EliminacionService $eliminacion)
    {
        $request->validate(['password' => ['required', 'string']], ['password.required' => 'Escribe tu contraseña para confirmar.']);

        /** @var User $usuario */
        $usuario = $request->user();
        $esDueno = strtolower($usuario->rol->nombre ?? '') === 'administrador';

        if (! Hash::check($request->password, $usuario->{self::COLUMNA_PASSWORD})) {
            $mensaje = 'La contraseña no es correcta. Si entras con Google, crea una con "¿Olvidaste tu contraseña?" en el inicio de sesión.';

            return response()->json(['message' => $mensaje, 'errors' => ['password' => [$mensaje]]], 422);
        }
        if ($esDueno && strtolower(trim((string) $request->input('confirmacion'))) !== $usuario->correo) {
            $mensaje = 'Escribe tu correo exactamente como aparece para confirmar que quieres borrar tus negocios.';

            return response()->json(['message' => $mensaje, 'errors' => ['confirmacion' => [$mensaje]]], 422);
        }

        $esDueno ? $eliminacion->eliminarDueno($usuario) : $eliminacion->eliminarEmpleado($usuario);

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flash('status', __('Your account was deleted.'));

        return response()->json(['redirect' => route('login')]);
    }
}