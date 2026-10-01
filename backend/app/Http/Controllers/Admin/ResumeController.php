<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Venta;

class ResumeController extends Controller
{
    public function index()
    {
        return [
            'sucursales' => Sucursal::count(),
            'usuarios' => [
                'total'           => User::count(),
                'administradores' => User::where('id_rol', 1)->count(),
                'gerentes'        => User::where('id_rol', 2)->count(),
                'cajeros'         => User::where('id_rol', 3)->count(),
            ],
            'productos'   => Producto::count(),
            // Netas: se resta lo devuelto/cancelado
            'ventas_hoy'  => Venta::totalNeto(Venta::whereDate('fecha_hora', now()->toDateString())),
            'ventas_mes'  => Venta::totalNeto(Venta::whereMonth('fecha_hora', now()->month)->whereYear('fecha_hora', now()->year)),
            'stock_bajo'  => Inventario::whereColumn('existencias', '<=', 'stock_minimo')->count(),
        ];
    }
}
