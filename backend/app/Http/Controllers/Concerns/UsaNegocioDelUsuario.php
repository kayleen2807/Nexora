<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Cada negocio es independiente: nadie lee ni modifica datos de otro negocio.
 * Este trait centraliza cómo se obtiene el negocio del usuario logueado y cómo se
 * comprueba que un registro (sucursal, producto, usuario…) le pertenece.
 */
trait UsaNegocioDelUsuario
{
    protected function usuario(Request $request): User
    {
        /** @var User $usuario */
        $usuario = $request->user();

        return $usuario;
    }

    /** id_negocio del usuario logueado; responde 422 si su cuenta no tiene negocio. */
    protected function idNegocio(Request $request): int
    {
        $idNegocio = $this->usuario($request)->id_negocio;

        abort_if(! $idNegocio, 422, 'Tu cuenta no está asociada a ningún negocio.');

        return (int) $idNegocio;
    }

    /** 404 si el registro es de otro negocio (no revelamos que existe). */
    protected function asegurarDelNegocio(Request $request, Model $modelo): void
    {
        abort_if((int) $modelo->getAttribute('id_negocio') !== $this->idNegocio($request), 404);
    }
}
