<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    use UsaNegocioDelUsuario;

    /** Ventas de las sucursales del negocio del admin. */
    public function index(Request $request)
    {
        $idNegocio = $this->idNegocio($request);

        return Venta::with(['cajero', 'metodoPago', 'devoluciones'])
            ->whereIn('id_sucursal', fn ($q) => $q->select('id_sucursal')->from('sucursal')->where('id_negocio', $idNegocio))
            ->orderByDesc('fecha_hora')
            ->get()
            ->map(fn (Venta $v) => [
                'id_sucursal' => $v->id_sucursal,
                'fecha'       => $v->fecha_hora?->toIso8601String(),
                'cajero'      => trim(($v->cajero->nombre ?? '') . ' ' . ($v->cajero->apellido ?? '')),
                'metodo_pago' => $v->metodoPago->nombre ?? '—',
                // Neto: lo cobrado menos lo devuelto/cancelado
                'total'       => round((float) $v->total - $v->montoDevuelto(), 2),
                'estado'      => $v->estado,
                'devuelto'    => $v->montoDevuelto(),
            ]);
    }
}
