<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;

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
            'ventas_hoy'  => (float) Venta::whereDate('fecha_hora', now()->toDateString())->sum('total'),
            'ventas_mes'  => (float) Venta::whereMonth('fecha_hora', now()->month)->whereYear('fecha_hora', now()->year)->sum('total'),
            'stock_bajo'  => Inventario::whereColumn('existencias', '<=', 'stock_minimo')->count(),
        ];
    }
}
