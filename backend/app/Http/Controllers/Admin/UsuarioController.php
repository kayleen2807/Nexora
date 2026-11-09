<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\UserInvitationService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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
            ];
        });
    }

    /** Regla: la sucursal elegida debe ser del mismo negocio. */
    private function reglaSucursal(Request $request): array
    {
        return ['nullable', 'integer', Rule::exists('sucursal', 'id_sucursal')->where('id_negocio', $this->idNegocio($request))];
    }

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
            'id_sucursal.exists' => 'La sucursal no pertenece a tu negocio.',
            'fecha_nacimiento.required' => __('Date of birth is required.'),
            'fecha_nacimiento.date' => __('Date of birth is invalid.'),
            'fecha_nacimiento.before_or_equal' => __('You must be at least 18 years old to register.'),
            'correo.unique' => __('This email address is already registered.'),
        ]);

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

        return response()->json(['message' => __('Invitation sent successfully.')], 201);
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

    public function destroy(Request $request, User $usuario)
    {
        $this->asegurarEditable($request, $usuario, 'eliminar');

        try {
            DB::transaction(function () use ($usuario) {
                Sucursal::where('id_gerente', $usuario->id_usuario)
                    ->update(['id_gerente' => null]);

                // user_invitations no tiene llave foránea: se limpian a mano para no dejar huérfanas
                UserInvitation::where('user_id', $usuario->id_usuario)->delete();

                $usuario->delete();
            });
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede eliminar: el usuario tiene ventas o cortes de caja registrados.',
            ], 422);
        }

        return response()->noContent();
    }
}