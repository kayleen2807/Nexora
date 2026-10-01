<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CorteCaja;

class CorteController extends Controller
{
    /** Historial de cortes de TODAS las sucursales (el panel filtra por sucursal). */
    public function index()
    {
        return CorteCaja::with(['caja', 'usuario'])
            ->orderByDesc('fecha_apertura')
            ->get()
            ->map(fn (CorteCaja $c) => $c->resumen());
    }
}
