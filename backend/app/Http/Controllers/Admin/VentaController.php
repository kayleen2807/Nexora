<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venta;

class VentaController extends Controller
{
    public function index()
    {
        return Venta::with(['cajero', 'metodoPago'])
            ->orderByDesc('fecha_hora')
            ->get()
            ->map(fn ($v) => [
                'id_sucursal' => $v->id_sucursal,
                'fecha'       => $v->fecha_hora,
                'cajero'      => trim(($v->cajero->nombre ?? '') . ' ' . ($v->cajero->apellido ?? '')),
                'metodo_pago' => $v->metodoPago->nombre ?? '—',
                'total'       => $v->total,
            ]);
    }
}
