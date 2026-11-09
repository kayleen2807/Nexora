<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;

class ResumeController extends Controller
{
    use UsaNegocioDelUsuario;

    /** Totales SOLO del negocio del admin. */
    public function index(Request $request)
    {
        $idNegocio = $this->idNegocio($request);
        $susSucursales = fn ($q) => $q->select('id_sucursal')->from('sucursal')->where('id_negocio', $idNegocio);
        $usuarios = fn () => User::where('id_negocio', $idNegocio);
        $ventas = fn () => Venta::whereIn('id_sucursal', $susSucursales);

        return [
            'sucursales' => Sucursal::where('id_negocio', $idNegocio)->count(),
            'usuarios' => [
                'total'           => $usuarios()->count(),
                'administradores' => $usuarios()->where('id_rol', 1)->count(),
                'gerentes'        => $usuarios()->where('id_rol', 2)->count(),
                'cajeros'         => $usuarios()->where('id_rol', 3)->count(),
            ],
            'productos'   => Producto::where('id_negocio', $idNegocio)->count(),
            // Netas: se resta lo devuelto/cancelado
            'ventas_hoy'  => Venta::totalNeto($ventas()->whereDate('fecha_hora', now()->toDateString())),
            'ventas_mes'  => Venta::totalNeto($ventas()->whereMonth('fecha_hora', now()->month)->whereYear('fecha_hora', now()->year)),
            'stock_bajo'  => Inventario::whereIn('id_sucursal', $susSucursales)->whereColumn('existencias', '<=', 'stock_minimo')->count(),
        ];
    }
}
