<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Concerns\EnviaReportes;
use App\Http\Controllers\Controller;
use App\Models\Reporte;
use Illuminate\Http\Request;

/**
 * El gerente envía reportes al administrador y ve los de SU sucursal (los suyos y los de sus cajeros).
 * Los de sus cajeros los ATIENDE él (estado y respuesta) y, si no puede resolverlos, los pasa al
 * administrador. Nunca ve ni toca los de otra sucursal.
 */
class ReporteController extends Controller
{
    use EnviaReportes;

    public function index(Request $request)
    {
        $idUsuario = $this->usuario($request)->id_usuario;

        $reportes = Reporte::with('autor.rol', 'responde.rol', 'sucursal')
            ->where('id_negocio', $this->idNegocio($request))
            ->where('id_sucursal', $this->idSucursal($request))
            ->orderByDesc('fecha')
            ->orderByDesc('id_reporte')
            ->get();

        return [
            // Globo rojo del menú: los de cajeros que esperan al gerente
            'pendientes' => $reportes->filter(fn (Reporte $r) => $this->loAtiendeEl($r, $idUsuario) && $r->estado === 'pendiente')->count(),
            'reportes' => $reportes->map(fn (Reporte $r) => $r->aArreglo($idUsuario) + [
                'puede_atender' => $this->loAtiendeEl($r, $idUsuario),
            ])->values(),
        ];
    }

    public function store(Request $request)
    {
        return $this->guardarReporte($request);
    }

    /** Cambia el estado y/o responde un reporte de uno de sus cajeros. */
    public function update(Request $request, Reporte $reporte)
    {
        $idUsuario = $this->reporteQueAtiende($request, $reporte);

        $data = $request->validate(Reporte::reglasAtender(), Reporte::mensajesAtender());
        $reporte->atender($data, $idUsuario);

        return $reporte->load('autor.rol', 'responde.rol', 'sucursal')->aArreglo($idUsuario) + ['puede_atender' => true];
    }

    /** "Pasar al administrador": desde ahora lo atiende el admin (vuelve a pendiente para que lo vea en su globo). */
    public function escalar(Request $request, Reporte $reporte)
    {
        $idUsuario = $this->reporteQueAtiende($request, $reporte);
        abort_if($reporte->estado === 'resuelto', 422, 'Este reporte ya está resuelto; no hace falta pasarlo al administrador.');

        $reporte->destino = Reporte::DESTINO_ADMIN;
        $reporte->fecha_escalado = now();
        $reporte->estado = 'pendiente';
        $reporte->save();

        return $reporte->load('autor.rol', 'responde.rol', 'sucursal')->aArreglo($idUsuario) + ['puede_atender' => false];
    }

    /** ¿Este reporte lo atiende el gerente? = de un cajero (no suyo) y todavía con destino 'gerente'. */
    private function loAtiendeEl(Reporte $reporte, int $idUsuario): bool
    {
        return $reporte->destino === Reporte::DESTINO_GERENTE && (int) $reporte->id_usuario !== $idUsuario;
    }

    /**
     * 404 si el reporte no es de su sucursal (como si no existiera); 422 si es de su sucursal pero no le toca
     * (ya lo pasó al administrador, o es uno que él mismo envió). Devuelve el id del gerente.
     */
    private function reporteQueAtiende(Request $request, Reporte $reporte): int
    {
        abort_unless((int) $reporte->id_negocio === $this->idNegocio($request)
            && (int) $reporte->id_sucursal === $this->idSucursal($request), 404);

        $idUsuario = $this->usuario($request)->id_usuario;
        abort_unless($this->loAtiendeEl($reporte, $idUsuario), 422,
            'Este reporte lo atiende el administrador; aquí solo puedes consultarlo.');

        return $idUsuario;
    }
}
