<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Concerns\UsaSucursalDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\CorteCaja;
use Illuminate\Http\Request;

class CorteController extends Controller
{
    use UsaSucursalDelUsuario;

    private const POR_PAGINA = 50;

    /**
     * Historial de cortes (turnos) de las cajas de la sucursal del gerente, más recientes primero,
     * de 50 en 50 (?pagina=N). `hoy` = totales netos de los turnos abiertos hoy (tarjetas del panel),
     * calculados aparte para que no dependan de la página que se esté viendo.
     */
    public function index(Request $request)
    {
        $idSucursal = $this->idSucursal($request);
        $deSucursal = fn () => CorteCaja::whereHas('caja', fn ($q) => $q->where('id_sucursal', $idSucursal));

        $pagina = $deSucursal()
            ->orderByDesc('fecha_apertura')
            ->orderByDesc('id_corte')
            ->paginate(self::POR_PAGINA, ['*'], 'pagina');

        $hoy = CorteCaja::resumenes($deSucursal()->where('fecha_apertura', '>=', today())->get());
        $neto = fn (string $metodo) => round($hoy->sum(fn ($c) => $c["ventas_$metodo"] - $c["devoluciones_$metodo"]), 2);

        return [
            'cortes' => CorteCaja::resumenes($pagina->getCollection()),
            'pagina' => $pagina->currentPage(),
            'paginas' => $pagina->lastPage(),
            'total' => $pagina->total(),
            'hoy' => ['efectivo' => $neto('efectivo'), 'tarjeta' => $neto('tarjeta')],
        ];
    }
}
