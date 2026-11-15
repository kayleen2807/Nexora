<?php

/*
 * EJEMPLO para adaptar. Atiende la sección "Configuración" de los tres paneles (cajero, gerente y administrador).
 *
 * IDEA CLAVE: nunca se manda el correo desde la pantalla. El usuario que cambia su nombre o su contraseña es
 * el que inició sesión (con su correo), y se obtiene del servidor con $request->user(). Así nadie puede cambiar
 * la cuenta de otra persona, ni manipulando la petición.
 */

namespace App\Http\Controllers;

use App\Services\DosPasosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
     * POST /configuracion/dos-pasos/codigo {activar: bool} — manda al correo el código para confirmar el cambio.
     * Activar o desactivar SIEMPRE pide un código: así se comprueba que el correo sí recibe los códigos
     * (si no, al activarla nadie podría entrar) y que quien la apaga es la dueña del correo.
     */
    public function enviarCodigoDosPasos(Request $request, DosPasosService $dosPasos)
    {
        $data = $request->validate(['activar' => ['required', 'boolean']]);
        $usuario = $request->user();
        $activar = (bool) $data['activar'];

        abort_if($activar === (bool) $usuario->dos_pasos_activo, 422,
            $activar ? 'La verificación en dos pasos ya está activada.' : 'La verificación en dos pasos ya está desactivada.');

        $espera = $dosPasos->esperaParaEnviar($usuario);
        abort_if($espera > 0, 429, "Espera {$espera} segundos para pedir otro código.");

        $dosPasos->enviarCodigo($usuario, $activar ? 'activar' : 'desactivar');

        return response()->json(['ok' => true, 'minutos' => DosPasosService::MINUTOS_VIGENCIA]);
    }

    /* PUT /configuracion/dos-pasos {activar: bool, codigo} — confirma el cambio con el código del correo */
    public function confirmarDosPasos(Request $request, DosPasosService $dosPasos)
    {
        $data = $request->validate([
            'activar' => ['required', 'boolean'],
            'codigo' => ['required', 'digits:6'],
        ], [
            'codigo.required' => 'Escribe el código que te llegó al correo.',
            'codigo.digits' => 'El código tiene 6 dígitos.',
        ]);
        $usuario = $request->user();

        $error = $dosPasos->verificar($usuario, $data['codigo']);
        if ($error) {
            return response()->json(['message' => $error, 'errors' => ['codigo' => [$error]]], 422);
        }

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
}