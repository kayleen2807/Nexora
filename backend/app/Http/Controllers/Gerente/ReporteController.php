<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Concerns\EnviaReportes;
use App\Http\Controllers\Controller;
use App\Models\Reporte;
use Illuminate\Http\Request;

/**
 * El gerente envía reportes al administrador y, en solo lectura, ve los de SU sucursal
 * (los suyos y los de sus cajeros). Nunca los de otra sucursal.
 */
class ReporteController extends Controller
{
    use EnviaReportes;

    public function index(Request $request)
    {
        $idUsuario = $this->usuario($request)->id_usuario;

        return Reporte::with('autor.rol', 'sucursal')
            ->where('id_negocio', $this->idNegocio($request))
            ->where('id_sucursal', $this->idSucursal($request))
            ->orderByDesc('fecha')
            ->orderByDesc('id_reporte')
            ->get()
            ->map(fn (Reporte $r) => $r->aArreglo($idUsuario));
    }

    public function store(Request $request)
    {
        return $this->guardarReporte($request);
    }
}
