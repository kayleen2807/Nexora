<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Gerente y Cajero solo operan sobre SU sucursal. Este trait centraliza cómo se
 * obtiene y se valida, para que ningún controlador pueda olvidarse del filtro.
 */
trait UsaSucursalDelUsuario
{
    protected function usuario(Request $request): User
    {
        /** @var User $usuario */
        $usuario = $request->user();

        return $usuario;
    }

    /** id_sucursal del usuario logueado; responde 422 si no tiene una asignada. */
    protected function idSucursal(Request $request): int
    {
        $idSucursal = $this->usuario($request)->id_sucursal;

        abort_if(! $idSucursal, 422, 'No tienes una sucursal asignada. Contacta a un administrador.');

        return (int) $idSucursal;
    }
}
