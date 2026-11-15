<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\DosPasosCodigoNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Verificación en dos pasos por correo. Un solo lugar para generar, enviar y revisar los códigos,
 * con los mismos límites que la verificación de correo (EmailVerificationController):
 *  - el código dura 10 minutos y se guarda encriptado con Hash (si alguien ve la BD, no ve el código);
 *  - al 5º código incorrecto se anula y hay que pedir otro (anti fuerza bruta);
 *  - se envían máximo 1 por minuto y 5 por hora a la misma cuenta (anti spam).
 */
class DosPasosService
{
    public const MINUTOS_VIGENCIA = 10;

    private const MAX_INTENTOS = 5;

    /** Para qué es el código: cambia el texto del correo. */
    public const MOTIVOS = ['login', 'activar', 'desactivar'];

    /** Segundos que faltan para poder mandar otro código (0 = ya se puede). */
    public function esperaParaEnviar(User $usuario): int
    {
        foreach ($this->llavesEnvio($usuario) as [$llave, $max]) {
            if (RateLimiter::tooManyAttempts($llave, $max)) {
                return RateLimiter::availableIn($llave);
            }
        }

        return 0;
    }

    /** ¿Tiene un código sin usar y sin vencer? (al iniciar sesión no se manda otro si ya tiene uno) */
    public function tieneCodigoVigente(User $usuario): bool
    {
        return $usuario->dos_pasos_codigo && $usuario->dos_pasos_expira && now()->lessThan($usuario->dos_pasos_expira);
    }

    /** Genera un código nuevo (anula el anterior), lo guarda encriptado y lo manda al correo. */
    public function enviarCodigo(User $usuario, string $motivo): void
    {
        foreach ($this->llavesEnvio($usuario) as [$llave, , $segundos]) {
            RateLimiter::hit($llave, $segundos);
        }

        $codigo = (string) random_int(100000, 999999);

        // forceFill: estas columnas no están en $fillable a propósito (nadie las puede mandar en un formulario)
        $usuario->forceFill([
            'dos_pasos_codigo' => Hash::make($codigo),   // es el código, NO la contraseña (esa la encripta el cast 'hashed')
            'dos_pasos_expira' => now()->addMinutes(self::MINUTOS_VIGENCIA),
        ])->save();
        RateLimiter::clear($this->llaveIntentos($usuario));   // código nuevo = intentos desde cero

        // El correo sale en el idioma que el usuario tiene guardado
        $usuario->notify((new DosPasosCodigoNotification($codigo, $motivo))->locale($usuario->idioma ?: config('app.locale')));
    }

    /**
     * Revisa el código. Devuelve null si es correcto (y lo borra: es de un solo uso)
     * o el mensaje de error para mostrar.
     */
    public function verificar(User $usuario, string $codigo): ?string
    {
        if (! $usuario->dos_pasos_codigo || ! $usuario->dos_pasos_expira || now()->greaterThan($usuario->dos_pasos_expira)) {
            return __('The verification code has expired.');
        }

        $llave = $this->llaveIntentos($usuario);

        if (! Hash::check($codigo, $usuario->dos_pasos_codigo)) {
            RateLimiter::hit($llave, self::MINUTOS_VIGENCIA * 60);

            if (RateLimiter::tooManyAttempts($llave, self::MAX_INTENTOS)) {
                $this->borrarCodigo($usuario);
                RateLimiter::clear($llave);

                return __('Too many incorrect codes. Request a new code.');
            }

            return __('The verification code is incorrect.');
        }

        RateLimiter::clear($llave);
        $this->borrarCodigo($usuario);

        return null;
    }

    /** Activa o desactiva la verificación en dos pasos de la cuenta. */
    public function cambiarEstado(User $usuario, bool $activo): void
    {
        $usuario->forceFill(['dos_pasos_activo' => $activo])->save();
    }

    private function borrarCodigo(User $usuario): void
    {
        $usuario->forceFill(['dos_pasos_codigo' => null, 'dos_pasos_expira' => null])->save();
    }

    private function llaveIntentos(User $usuario): string
    {
        return 'dos-pasos-intentos|'.$usuario->id_usuario;
    }

    /** @return array<int, array{0: string, 1: int, 2: int}> [llave, máximo, segundos] */
    private function llavesEnvio(User $usuario): array
    {
        return [
            ['dos-pasos-envio-min|'.$usuario->id_usuario, 1, 60],
            ['dos-pasos-envio-hora|'.$usuario->id_usuario, 5, 3600],
        ];
    }
}
