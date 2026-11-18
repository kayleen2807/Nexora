<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Producto;
use Illuminate\Http\Request;

/**
 * Gerente y Cajero solo operan sobre SU sucursal (y por lo tanto sobre su negocio).
 * Este trait centraliza cómo se obtiene y se valida, para que ningún controlador
 * pueda olvidarse del filtro. Hereda usuario(), idNegocio() y asegurarDelNegocio().
 */
trait UsaSucursalDelUsuario
{
    use UsaNegocioDelUsuario;

    /** id_sucursal del usuario logueado; responde 422 si no tiene una asignada. */
    protected function idSucursal(Request $request): int
    {
        $idSucursal = $this->usuario($request)->id_sucursal;

        abort_if(! $idSucursal, 422, 'No tienes una sucursal asignada. Contacta a un administrador.');

        return (int) $idSucursal;
    }

    /**
     * 404 si el producto es de otro negocio, o si es un producto PENDIENTE / RECHAZADO que dio de alta
     * OTRA sucursal (para esta sucursal no existe: ver Producto::scopeVisibleEnSucursal).
     */
    protected function asegurarProductoVisible(Request $request, Producto $producto): void
    {
        $this->asegurarDelNegocio($request, $producto);
        abort_unless($producto->visibleEnSucursal($this->idSucursal($request)), 404);
    }
}
