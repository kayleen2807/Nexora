<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Concerns\EnviaReportes;
use App\Http\Controllers\Controller;
use App\Models\Reporte;
use Illuminate\Http\Request;

/** El cajero envía reportes al administrador y ve SOLO los suyos (con la respuesta). */
class ReporteController extends Controller
{
    use EnviaReportes;

    public function index(Request $request)
    {
        $idUsuario = $this->usuario($request)->id_usuario;

        return Reporte::with('autor.rol', 'sucursal')
            ->where('id_usuario', $idUsuario)
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
