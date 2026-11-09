<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Reporte;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Alta de reportes, igual para gerente y cajero: el reporte queda ligado a SU negocio
 * y SU sucursal (nunca se toman del formulario) y llega al administrador de ese negocio.
 */
trait EnviaReportes
{
    use UsaSucursalDelUsuario;

    /** Máximo de reportes por usuario por hora (evita llenar la bandeja del admin por error o abuso). */
    private const REPORTES_POR_HORA = 10;

    protected function guardarReporte(Request $request): JsonResponse
    {
        $usuario = $this->usuario($request);
        $data = $request->validate(Reporte::reglas(), Reporte::mensajes());

        $llave = 'reporte|'.$usuario->id_usuario;
        abort_if(RateLimiter::tooManyAttempts($llave, self::REPORTES_POR_HORA), 422,
            'Ya enviaste muchos reportes en la última hora. Intenta más tarde.');
        RateLimiter::hit($llave, 3600);

        $reporte = Reporte::create([
            'fecha' => now(),
            'id_negocio' => $this->idNegocio($request),
            'id_sucursal' => $this->idSucursal($request),
            'id_usuario' => $usuario->id_usuario,
            'tipo' => $data['tipo'],
            'asunto' => trim($data['asunto']),
            'descripcion' => trim($data['descripcion']),
            'estado' => 'pendiente',
        ]);

        return response()->json($reporte->load('autor.rol', 'sucursal')->aArreglo($usuario->id_usuario), 201);
    }
}
