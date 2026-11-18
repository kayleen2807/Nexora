<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Reporte;
use Illuminate\Http\Request;

/**
 * Bandeja de reportes del administrador, solo de SU negocio: los de sus gerentes, los que un gerente
 * le pasó y los de cajeros de una sucursal sin gerente (Reporte::scopeParaAdmin). Los demás de cajeros
 * los atiende el gerente.
 */
class ReporteController extends Controller
{
    use UsaNegocioDelUsuario;

    /** Reportes de su bandeja (los filtros de estado/tipo/sucursal se aplican en el panel) + pendientes. */
    public function index(Request $request)
    {
        $idNegocio = $this->idNegocio($request);

        $reportes = Reporte::with('autor.rol', 'responde.rol', 'sucursal')
            ->paraAdmin($idNegocio)
            ->orderByDesc('fecha')
            ->orderByDesc('id_reporte')
            ->get();

        return [
            'pendientes' => $reportes->where('estado', 'pendiente')->count(),
            'reportes' => $reportes->map(fn (Reporte $r) => $r->aArreglo())->values(),
        ];
    }

    /** Cambia el estado y/o guarda la respuesta para quien lo envió. */
    public function update(Request $request, Reporte $reporte)
    {
        // Solo los de su bandeja: uno de otro negocio, o que está con un gerente, → 404
        abort_unless(Reporte::paraAdmin($this->idNegocio($request))->whereKey($reporte->id_reporte)->exists(), 404);

        $data = $request->validate(Reporte::reglasAtender(), Reporte::mensajesAtender());
        $reporte->atender($data, $this->usuario($request)->id_usuario);

        return $reporte->load('autor.rol', 'responde.rol', 'sucursal')->aArreglo();
    }
}
