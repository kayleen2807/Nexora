<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use App\Services\ReporteVentasService;
use Illuminate\Http\Request;

/** GET /administrador/reportes-ventas?desde&hasta&sucursal — todas las sucursales de SU negocio, o una. */
class ReporteVentasController extends Controller
{
    use UsaNegocioDelUsuario;

    public function index(Request $request, ReporteVentasService $reportes)
    {
        [$desde, $hasta] = $reportes->rango($request);
        $request->validate(['sucursal' => ['nullable', 'integer']]);

        $sucursales = Sucursal::where('id_negocio', $this->idNegocio($request))
            ->when($request->filled('sucursal'), fn ($q) => $q->where('id_sucursal', $request->integer('sucursal')))
            ->pluck('id_sucursal')->all();
        // Una sucursal de otro negocio simplemente no aparece: el reporte sale en ceros (no se revela que existe)

        return response()->json($reportes->generar($sucursales, $desde, $hasta));
    }
}
