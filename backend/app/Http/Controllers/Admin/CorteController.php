<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\CorteCaja;
use Illuminate\Http\Request;

class CorteController extends Controller
{

    private const POR_PAGINA = 50;

    /**
     * Historial de cortes de las sucursales DEL NEGOCIO, de 50 en 50 (?pagina=N).
     * ?sucursal=ID filtra en el servidor (antes llegaban todos y el panel filtraba).
     * Una sucursal de otro negocio simplemente no devuelve nada.
     */
    public function index(Request $request)
    {
        $idNegocio = $this->idNegocio($request);
        $data = $request->validate(['sucursal' => ['nullable', 'integer']]);

        $pagina = CorteCaja::whereHas('caja.sucursal', fn ($q) => $q->where('id_negocio', $idNegocio))
            ->when($data['sucursal'] ?? null, fn ($q, $id) => $q->whereHas('caja', fn ($c) => $c->where('id_sucursal', $id)))
            ->orderByDesc('fecha_apertura')
            ->orderByDesc('id_corte')
            ->paginate(self::POR_PAGINA, ['*'], 'pagina');

        return [
            'cortes' => CorteCaja::resumenes($pagina->getCollection()),
            'pagina' => $pagina->currentPage(),
            'paginas' => $pagina->lastPage(),
            'total' => $pagina->total(),
        ];
    }
}
