<?php

/*
 * EJEMPLO para adaptar. Atiende la sección "Configuración" de los tres paneles (cajero, gerente y administrador).
 *
 * IDEA CLAVE: nunca se manda el correo desde la pantalla. El usuario que cambia su nombre o su contraseña es
 * el que inició sesión (con su correo), y se obtiene del servidor con $request->user(). Así nadie puede cambiar
 * la cuenta de otra persona, ni manipulando la petición.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ConfiguracionController extends Controller
{
    /** Columna donde se guarda la contraseña encriptada. Cámbiala si en tu tabla se llama distinto. */
    private const COLUMNA_PASSWORD = 'password';

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
        ]);
    }

    /* PUT /configuracion/perfil — guarda nombre y apellido del usuario con sesión iniciada */
    public function actualizarPerfil(Request $request)
    {
        $datos = $request->validate([
            'nombre'   => ['required', 'string', 'max:80'],
            'apellido' => ['nullable', 'string', 'max:80'],
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
            'password_nueva.regex'     => 'La contraseña debe tener mínimo 8 caracteres, una mayúscula, un número y un carácter especial.',
            'password_nueva.confirmed' => 'Las contraseñas no coinciden.',
            'password_nueva.different' => 'La nueva contraseña debe ser distinta a la actual.',
        ]);

        $usuario = $request->user();

        // 0) Cuentas que entran con Google, o invitaciones aún no aceptadas, no tienen contraseña guardada
        if (empty($usuario->{self::COLUMNA_PASSWORD})) {
            return response()->json([
                'message' => 'Tu cuenta no tiene una contraseña propia (entra con Google o aún no aceptas la invitación).',
            ], 422);
        }

        // 1) La contraseña actual tiene que ser correcta (se compara contra la encriptada guardada)
        if (!Hash::check($request->password_actual, $usuario->{self::COLUMNA_PASSWORD})) {
            return response()->json([
                'message' => 'La contraseña actual no es correcta.',
                'errors'  => ['password_actual' => ['La contraseña actual no es correcta.']],
            ], 422);
        }

        // 2) Se guarda ENCRIPTADA, nunca como texto normal
        $usuario->{self::COLUMNA_PASSWORD} = Hash::make($request->password_nueva);
        $usuario->save();

        // 3) Opcional: cierra la sesión en los demás dispositivos (requiere el middleware
        //    \Illuminate\Session\Middleware\AuthenticateSession en el grupo 'web')
        // Auth::logoutOtherDevices($request->password_nueva);

        return response()->json(['ok' => true]);
    }
}