<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;

class MiSucursalController extends Controller
{
    public function index(Request $request)
    {
        $sucursal = $request->user()->sucursal;

        if (! $sucursal) {
            return response()->json([
                'message' => 'No tienes una sucursal asignada todavía. Contacta a un administrador.',
            ], 404);
        }

        $idSucursal = $sucursal->id_sucursal;

        return [
            'id_sucursal' => $sucursal->id_sucursal,
            'nombre'      => $sucursal->nombre,
            'direccion'   => $sucursal->direccion,
            'contacto'    => $sucursal->contacto,
            'estado'      => $sucursal->estado,
            'empleados'   => User::where('id_sucursal', $idSucursal)->count(),
            'productos'   => Inventario::where('id_sucursal', $idSucursal)->count(),
            'stock_bajo'  => Inventario::where('id_sucursal', $idSucursal)
                                ->whereColumn('existencias', '<=', 'stock_minimo')->count(),
            // Netas: se resta lo devuelto/cancelado
            'ventas_hoy'  => Venta::totalNeto(Venta::where('id_sucursal', $idSucursal)
                                ->whereDate('fecha_hora', now()->toDateString())),
            'ventas_mes'  => Venta::totalNeto(Venta::where('id_sucursal', $idSucursal)
                                ->whereMonth('fecha_hora', now()->month)
                                ->whereYear('fecha_hora', now()->year)),
        ];
    }
}
