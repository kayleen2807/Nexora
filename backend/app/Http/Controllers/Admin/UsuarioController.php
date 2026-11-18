<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\UserInvitation;
use App\Notifications\CorreoCambiadoNotification;
use App\Services\EliminacionService;
use App\Services\EmailVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\UserInvitationService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class UsuarioController extends Controller
{
    use UsaNegocioDelUsuario;

    private const ROLES = [1 => 'admin', 2 => 'gerente', 3 => 'cajero'];
    private const ROLES_INVERSO = ['admin' => 1, 'gerente' => 2, 'cajero' => 3];

    /**
     * Solo las personas del negocio del admin. `esYo` marca la cuenta del propio admin e
     * `invitacionEstado` el estado de su última invitación por correo (sent, viewed, accepted,
     * expired, revoked) o, si nunca se le invitó, si ya verificó su correo.
     */
    public function index(Request $request)
    {
        $yo = $this->usuario($request)->id_usuario;

        $usuarios = User::where('id_negocio', $this->idNegocio($request))
            ->orderBy('id_usuario')
            ->get();
        $invitaciones = UserInvitation::whereIn('user_id', $usuarios->pluck('id_usuario'))
            ->latest('id')
            ->get()
            ->groupBy('user_id');

        return $usuarios->map(function ($u) use ($invitaciones, $yo) {
            $invitacion = $invitaciones->get($u->id_usuario)?->first();
            $estado = $invitacion?->status ?? ($u->email_verified_at ? 'verified' : 'pending');
            // Una invitación que venció sin abrirse se reporta como vencida aunque nadie la haya revisado
            if (in_array($estado, ['sent', 'viewed'], true) && $invitacion->expires_at?->isPast()) {
                $estado = 'expired';
            }

            return [
                'id' => $u->id_usuario,
                'nombre' => trim($u->nombre.' '.$u->apellido),
                'rol' => self::ROLES[$u->id_rol] ?? 'cajero',
                'sucursalId' => $u->id_sucursal,
                'esYo' => $u->id_usuario === $yo,
                'invitacionEstado' => $estado,
                // Para el formulario "Editar" (los datos por separado)
                'nombreSolo' => $u->nombre,
                'apellido' => $u->apellido,
                'correo' => $u->correo,
                'fechaNacimiento' => $u->fecha_nacimiento?->toDateString(),
            ];
        });
    }

    /**
     * Regla: la sucursal es OBLIGATORIA (los paneles de gerente y cajero trabajan con los datos de su
     * sucursal; sin ella no tienen nada que mostrar) y debe ser del mismo negocio.
     */
    private function reglaSucursal(Request $request): array
    {
        return ['required', 'integer', Rule::exists('sucursal', 'id_sucursal')->where('id_negocio', $this->idNegocio($request))];
    }

    private const MENSAJE_SUCURSAL = 'Elige la sucursal donde trabajará esta persona (si aún no tienes, créala en "Sucursales").';

    /**
     * El admin no puede cambiarse ni borrarse a sí mismo, y el dueño del negocio
     * (el único administrador) no puede ser modificado desde aquí.
     */
    private function asegurarEditable(Request $request, User $usuario, string $accion): void
    {
        $this->asegurarDelNegocio($request, $usuario);

        abort_if($usuario->id_usuario === $this->usuario($request)->id_usuario, 422,
            "No puedes {$accion} tu propia cuenta.");
        abort_if((int) $usuario->id_rol === self::ROLES_INVERSO['admin'], 422,
            'El administrador es el dueño del negocio y no se puede modificar.');
    }

    /** Mismos datos y reglas que el registro público (RegisterController), más rol y sucursal. */
    public function store(Request $request, UserInvitationService $invitationService)
    {
        $request->merge(['correo' => strtolower(trim((string) $request->input('correo')))]);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'apellido' => ['required', 'string', 'max:150'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:'.now()->subYears(18)->toDateString()],
            'correo' => ['required', 'email', 'max:150', 'unique:usuario,correo'],
            // Sin contraseña: la persona invitada la crea desde el enlace del correo (UserInvitationController)
            // Un negocio tiene un solo administrador (su dueño): aquí solo se crean empleados
            'rol' => ['required', 'in:gerente,cajero'],
            'id_sucursal' => $this->reglaSucursal($request),
        ], [
            'rol.in' => 'Solo puedes crear usuarios con rol Gerente o Cajero.',
            'id_sucursal.required' => self::MENSAJE_SUCURSAL,
            'id_sucursal.exists' => 'La sucursal no pertenece a tu negocio.',
            'fecha_nacimiento.required' => __('Date of birth is required.'),
            'fecha_nacimiento.date' => __('Date of birth is invalid.'),
            'fecha_nacimiento.before_or_equal' => __('You must be at least 18 years old to register.'),
            'correo.unique' => __('This email address is already registered.'),
        ]);

        // Crear la cuenta y mandar la invitación van JUNTOS: si el correo no sale (p. ej. contraseña de
        // aplicación de Gmail incorrecta), se deshace todo y no queda una cuenta que nadie puede activar.
        try {
            DB::transaction(function () use ($data, $request, $invitationService) {
                $usuario = User::create([
                    'nombre' => $data['nombre'],
                    'apellido' => $data['apellido'],
                    'fecha_nacimiento' => $data['fecha_nacimiento'],
                    'correo' => $data['correo'],
                    // Temporal e imposible de adivinar; se reemplaza cuando acepta la invitación (el cast la hashea)
                    'contrasena' => Str::random(40),
                    'id_rol' => self::ROLES_INVERSO[$data['rol']],
                    'id_sucursal' => $data['id_sucursal'] ?? null,
                    'id_negocio' => $this->idNegocio($request),
                ]);

                $invitationService->send($usuario, $this->usuario($request));
            });
        } catch (TransportExceptionInterface $e) {
            report($e);   // el detalle técnico queda en laravel.log

            return response()->json([
                'message' => 'No se pudo enviar la invitación a '.$data['correo'].'. Revisa la configuración del correo (.env) e inténtalo de nuevo; el usuario no se guardó.',
            ], 503);
        }

        return response()->json(['message' => __('Invitation sent successfully.'), 'correo' => $data['correo']], 201);
    }

    /**
     * Vuelve a mandar la invitación (la anterior queda revocada). Solo a empleados del
     * negocio que aún no la han aceptado; máximo una vez por minuto por usuario.
     */
    public function reenviarInvitacion(Request $request, User $usuario, UserInvitationService $invitationService)
    {
        $this->asegurarEditable($request, $usuario, 'reenviar la invitación de');

        $aceptada = UserInvitation::where('user_id', $usuario->id_usuario)->where('status', 'accepted')->exists();
        abort_if($aceptada || $usuario->email_verified_at, 422, 'Este usuario ya activó su cuenta; no necesita otra invitación.');

        $llave = 'reenvio-invitacion|'.$usuario->id_usuario;
        abort_if(RateLimiter::tooManyAttempts($llave, 1), 422,
            'Espera '.RateLimiter::availableIn($llave).' segundos antes de reenviar otra invitación a este usuario.');
        RateLimiter::hit($llave, 60);

        $invitationService->send($usuario, $this->usuario($request));

        return response()->json(['message' => __('Invitation sent successfully.')]);
    }

    public function actualizarRol(Request $request, User $usuario)
    {
        $this->asegurarEditable($request, $usuario, 'cambiar el rol de');

        $data = $request->validate([
            'rol' => ['required', 'in:gerente,cajero'],
            'id_sucursal' => $this->reglaSucursal($request),
        ], [
            'rol.in' => 'Solo se puede asignar el rol Gerente o Cajero.',
            'id_sucursal.required' => self::MENSAJE_SUCURSAL,
            'id_sucursal.exists' => 'La sucursal no pertenece a tu negocio.',
        ]);

        DB::transaction(function () use ($usuario, $data) {
            $nuevoRol = self::ROLES_INVERSO[$data['rol']];
            $nuevaSucursal = $data['id_sucursal'] ?? null;

            // Si deja de ser gerente o cambia de sucursal, ya no encabeza su sucursal anterior
            if ($nuevoRol !== self::ROLES_INVERSO['gerente'] || (int) $nuevaSucursal !== (int) $usuario->id_sucursal) {
                Sucursal::where('id_gerente', $usuario->id_usuario)->update(['id_gerente' => null]);
            }

            $usuario->update(['id_rol' => $nuevoRol, 'id_sucursal' => $nuevaSucursal]);
        });

        return response()->json($usuario->fresh());
    }

    public function destroy(Request $request, User $usuario, EliminacionService $eliminacion)
    {
        $this->asegurarEditable($request, $usuario, 'eliminar');

        // Sin historial se borra; con ventas/cortes se anonimiza (antes: 422 y no se podía) — EliminacionService
        $resultado = $eliminacion->eliminarEmpleado($usuario);

        return response()->json(['resultado' => $resultado]);
    }

    /**
     * PUT /administrador/usuarios/{usuario} — nombre, apellido, fecha de nacimiento y correo (decisión de Kay
     * 2026-11-17). Si cambia el correo: quien no ha aceptado la invitación la recibe en el nuevo; quien ya
     * estaba activo debe confirmar el nuevo con el código de 6 dígitos al entrar, y se avisa al correo viejo.
     * Todo va en una transacción con los correos: si alguno no sale, no se cambia nada.
     */
    public function update(Request $request, User $usuario, UserInvitationService $invitaciones, EmailVerificationService $verificacion)
    {
        $this->asegurarEditable($request, $usuario, 'editar');
        $request->merge(['correo' => strtolower(trim((string) $request->input('correo')))]);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'apellido' => ['required', 'string', 'max:150'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:'.now()->subYears(18)->toDateString()],
            'correo' => ['required', 'email', 'max:150', Rule::unique('usuario', 'correo')->ignore($usuario->id_usuario, 'id_usuario')],
        ], [
            'nombre.required' => __('Name is required.'),
            'apellido.required' => __('First surname is required.'),
            'fecha_nacimiento.required' => __('Date of birth is required.'),
            'fecha_nacimiento.date' => __('Date of birth is invalid.'),
            'fecha_nacimiento.before_or_equal' => __('You must be at least 18 years old to register.'),
            'correo.required' => __('Email address is required.'),
            'correo.email' => __('Email address must be valid.'),
            'correo.unique' => __('This email address is already registered.'),
        ]);

        $correoViejo = $usuario->correo;
        $cambiaCorreo = $data['correo'] !== $correoViejo;
        $activo = $usuario->email_verified_at !== null
            || UserInvitation::where('user_id', $usuario->id_usuario)->where('status', 'accepted')->exists();

        try {
            DB::transaction(function () use ($usuario, $data, $cambiaCorreo, $activo, $correoViejo, $request, $invitaciones, $verificacion) {
                $usuario->fill([
                    'nombre' => trim($data['nombre']),
                    'apellido' => trim($data['apellido']),
                    'fecha_nacimiento' => $data['fecha_nacimiento'],
                    'correo' => $data['correo'],
                ])->save();

                if (! $cambiaCorreo) {
                    return;
                }
                DB::table('password_reset_tokens')->where('email', $correoViejo)->delete();

                if (! $activo) {
                    $invitaciones->send($usuario, $this->usuario($request));   // anula la anterior

                    return;
                }
                // Activo: al entrar, LoginController lo manda a confirmar el correo nuevo con el código
                $usuario->forceFill(['email_verified_at' => null])->save();
                $verificacion->send($usuario);
                Notification::route('mail', $correoViejo)
                    ->notify((new CorreoCambiadoNotification($data['correo']))->locale($usuario->idioma ?: 'es'));
            });
        } catch (TransportExceptionInterface $e) {
            report($e);

            return response()->json([
                'message' => 'No se pudo enviar el correo a '.$data['correo'].'. No se guardó ningún cambio; inténtalo de nuevo.',
            ], 503);
        }

        return response()->json([
            'message' => $cambiaCorreo
                ? ($activo ? 'Datos guardados. Le enviamos un código al correo nuevo: lo pedirá la próxima vez que inicie sesión.' : 'Datos guardados. Le enviamos la invitación al correo nuevo.')
                : 'Datos guardados.',
        ]);
    }
}