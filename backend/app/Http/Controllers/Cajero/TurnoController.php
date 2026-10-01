<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Concerns\UsaSucursalDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Caja;
use App\Models\CorteCaja;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Turno del cajero = un registro en `corte_caja`.
 * Abrir: elige una caja libre y declara el fondo inicial.
 * Cerrar (corte): compara efectivo esperado (fondo + ventas en efectivo) contra lo contado.
 */
class TurnoController extends Controller
{
    use UsaSucursalDelUsuario;

    /** Todo lo que el panel necesita al cargar: turno actual, cajas libres y métodos de pago. */
    public function estado(Request $request)
    {
        $idSucursal = $this->idSucursal($request);
        $usuario = $this->usuario($request);
        $turno = CorteCaja::abiertoDe($usuario->id_usuario);

        $cajasLibres = Caja::where('id_sucursal', $idSucursal)
            ->where(fn ($q) => $q->whereNull('estado')->orWhere('estado', '!=', Caja::INACTIVA))
            ->whereDoesntHave('corteAbierto')
            ->orderBy('numero_caja')
            ->get()
            ->map(fn (Caja $c) => ['id' => $c->id_caja, 'numero' => $c->numero_caja]);

        return [
            'sucursal' => $usuario->sucursal->nombre ?? '',
            'cajero' => trim($usuario->nombre.' '.$usuario->apellido),
            'turno' => $turno ? $this->datosTurno($turno) : null,
            'cajas' => $cajasLibres,
            'metodos_pago' => MetodoPago::orderBy('id_metodo_pago')->get()
                ->map(fn (MetodoPago $m) => ['id' => $m->id_metodo_pago, 'nombre' => $m->nombre]),
        ];
    }

    public function abrir(Request $request)
    {
        $idSucursal = $this->idSucursal($request);
        $usuario = $this->usuario($request);

        $data = $request->validate([
            'id_caja' => ['required', 'integer'],
            'monto_inicial' => ['required', 'numeric', 'min:0'],
        ]);

        $turno = DB::transaction(function () use ($data, $idSucursal, $usuario) {
            abort_if(CorteCaja::abiertoDe($usuario->id_usuario) !== null, 422, 'Ya tienes un turno abierto.');

            // lockForUpdate: si dos cajeros eligen la misma caja al mismo tiempo, solo uno la obtiene
            $caja = Caja::where('id_caja', $data['id_caja'])
                ->where('id_sucursal', $idSucursal)
                ->lockForUpdate()
                ->first();

            abort_if(! $caja, 404, 'La caja no existe en tu sucursal.');
            abort_if($caja->estado === Caja::INACTIVA, 422, 'La caja está desactivada.');
            abort_if($caja->corteAbierto()->exists(), 422, 'Esa caja ya está en uso por otro cajero.');

            return CorteCaja::create([
                'fecha_apertura' => now(),
                'monto_inicial' => $data['monto_inicial'],
                'id_caja' => $caja->id_caja,
                'id_usuario' => $usuario->id_usuario,
            ])->load('caja');
        });

        return response()->json($this->datosTurno($turno), 201);
    }

    public function resumen(Request $request)
    {
        return $this->turnoActual($request)->resumen();
    }

    /** Corte de caja: guarda el efectivo contado y cierra el turno. */
    public function cerrar(Request $request)
    {
        $turno = $this->turnoActual($request);

        $data = $request->validate([
            'efectivo_contado' => ['required', 'numeric', 'min:0'],
        ]);

        $turno->update([
            'fecha_cierre' => now(),
            'monto_final' => $data['efectivo_contado'],
        ]);

        // Con el turno ya cerrado, resumen() incluye contado y diferencia
        return $turno->resumen();
    }

    private function turnoActual(Request $request): CorteCaja
    {
        $turno = CorteCaja::abiertoDe($this->usuario($request)->id_usuario);

        abort_if(! $turno, 422, 'No tienes un turno abierto.');

        return $turno;
    }

    private function datosTurno(CorteCaja $turno): array
    {
        return [
            'id' => $turno->id_corte,
            'id_caja' => $turno->id_caja,
            'caja' => $turno->caja?->numero_caja,
            'apertura' => $turno->fecha_apertura->toIso8601String(),
            'monto_inicial' => (float) $turno->monto_inicial,
        ];
    }
}
