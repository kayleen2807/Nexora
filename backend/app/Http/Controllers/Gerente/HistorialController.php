<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Concerns\UsaSucursalDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    use UsaSucursalDelUsuario;

    /** Ventas de la sucursal del gerente, más recientes primero, con lo devuelto/cancelado. */
    public function index(Request $request)
    {
        $idSucursal = $this->idSucursal($request);

        return Venta::with(['cajero', 'metodoPago', 'caja', 'devoluciones'])
            ->where('id_sucursal', $idSucursal)
            ->orderByDesc('fecha_hora')
            ->orderByDesc('id_venta')
            ->get()
            ->map(fn (Venta $v) => [
                'folio' => $v->id_venta,
                'fecha' => $v->fecha_hora?->toIso8601String(),
                'cajero' => trim(($v->cajero->nombre ?? '').' '.($v->cajero->apellido ?? '')) ?: '—',
                'caja' => $v->caja?->numero_caja,
                'metodo' => $v->metodoPago->nombre ?? '—',
                'total' => (float) $v->total,
                'devuelto' => $v->montoDevuelto(),
                'neto' => round((float) $v->total - $v->montoDevuelto(), 2),
                'estado' => $v->estado,
            ]);
    }
}
