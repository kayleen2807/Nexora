<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Concerns\UsaSucursalDelUsuario;
use App\Http\Controllers\Controller;
use App\Services\ReporteVentasService;
use Illuminate\Http\Request;

/** GET /gerente/reportes-ventas?desde&hasta — SIEMPRE solo su sucursal (regla del panel Gerente). */
class ReporteVentasController extends Controller
{
    use UsaSucursalDelUsuario;

    public function index(Request $request, ReporteVentasService $reportes)
    {
        [$desde, $hasta] = $reportes->rango($request);

        return response()->json($reportes->generar([$this->idSucursal($request)], $desde, $hasta));
    }
}
