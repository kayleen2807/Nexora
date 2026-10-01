<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Concerns\UsaSucursalDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\CorteCaja;
use Illuminate\Http\Request;

class CorteController extends Controller
{
    use UsaSucursalDelUsuario;

    /** Historial de cortes (turnos) de las cajas de la sucursal del gerente, más recientes primero. */
    public function index(Request $request)
    {
        $idSucursal = $this->idSucursal($request);

        return CorteCaja::with(['caja', 'usuario'])
            ->whereHas('caja', fn ($q) => $q->where('id_sucursal', $idSucursal))
            ->orderByDesc('fecha_apertura')
            ->get()
            ->map(fn (CorteCaja $c) => $c->resumen());
    }
}
