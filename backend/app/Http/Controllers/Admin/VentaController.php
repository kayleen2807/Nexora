<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venta;

class VentaController extends Controller
{
    public function index()
    {
        return Venta::with(['cajero', 'metodoPago', 'devoluciones'])
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
