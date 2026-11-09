<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Reporte;
use Illuminate\Http\Request;

/** Bandeja de reportes del administrador: solo los de SU negocio. */
class ReporteController extends Controller
{
    use UsaNegocioDelUsuario;

    /** Todos los reportes del negocio (los filtros de estado/tipo/sucursal se aplican en el panel) + pendientes. */
    public function index(Request $request)
    {
        $idNegocio = $this->idNegocio($request);

        $reportes = Reporte::with('autor.rol', 'sucursal')
            ->where('id_negocio', $idNegocio)
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
        $this->asegurarDelNegocio($request, $reporte);

        $data = $request->validate([
            'estado' => ['required', 'in:'.implode(',', Reporte::ESTADOS)],
            'respuesta' => ['nullable', 'string', 'max:500'],
        ], [
            'estado.in' => 'El estado no es válido.',
            'respuesta.max' => 'La respuesta no puede tener más de 500 caracteres.',
        ]);

        $reporte->estado = $data['estado'];

        $respuesta = trim((string) ($data['respuesta'] ?? ''));
        if ($respuesta !== '' && $respuesta !== $reporte->respuesta) {
            $reporte->respuesta = $respuesta;
            $reporte->fecha_respuesta = now();
            $reporte->id_usuario_responde = $this->usuario($request)->id_usuario;
        }
        $reporte->save();

        return $reporte->load('autor.rol', 'sucursal')->aArreglo();
    }
}
