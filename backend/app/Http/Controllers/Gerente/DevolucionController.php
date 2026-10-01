<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Concerns\UsaSucursalDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\DetalleVenta;
use App\Models\Devolucion;
use App\Models\Inventario;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Solo el gerente puede devolver piezas o cancelar ventas de SU sucursal, siempre con motivo.
 * Nada se borra: queda registro en `devolucion` y en las columnas de cancelación de `venta`.
 */
class DevolucionController extends Controller
{
    use UsaSucursalDelUsuario;

    /** Detalle de una venta con lo que ya se devolvió de cada línea. */
    public function show(Request $request, Venta $venta)
    {
        $this->asegurarPropia($request, $venta);

        $venta->load(['detalles.producto', 'devoluciones.usuario', 'cajero', 'metodoPago', 'caja']);
        $devueltoPorLinea = $venta->devoluciones->groupBy('id_detalle')->map->sum('cantidad');

        return [
            'folio' => $venta->id_venta,
            'fecha' => $venta->fecha_hora?->toIso8601String(),
            'estado' => $venta->estado,
            'motivo_cancelacion' => $venta->motivo_cancelacion,
            'total' => (float) $venta->total,
            'devuelto' => $venta->montoDevuelto(),
            'items' => $venta->detalles->map(fn (DetalleVenta $d) => [
                'id_detalle' => $d->id_detalle,
                'producto' => $d->producto->nombre ?? 'Producto eliminado',
                'cantidad' => $d->cantidad,
                'devuelto' => (int) ($devueltoPorLinea[$d->id_detalle] ?? 0),
                'disponible' => $d->cantidad - (int) ($devueltoPorLinea[$d->id_detalle] ?? 0),
                'precio_unitario' => (float) $d->precio_unitario,
            ])->values(),
            'devoluciones' => $venta->devoluciones->sortByDesc('fecha')->values()->map(fn (Devolucion $d) => [
                'fecha' => $d->fecha?->toIso8601String(),
                'tipo' => $d->tipo,
                'producto' => $venta->detalles->firstWhere('id_detalle', $d->id_detalle)?->producto?->nombre,
                'cantidad' => $d->cantidad,
                'monto' => (float) $d->monto,
                'motivo' => $d->motivo,
                'usuario' => $d->usuario ? trim($d->usuario->nombre.' '.$d->usuario->apellido) : null,
            ]),
        ];
    }

    /** Devolución parcial: algunas piezas de algunas líneas. */
    public function devolver(Request $request, Venta $venta)
    {
        $this->asegurarPropia($request, $venta);

        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_detalle' => ['required', 'integer', 'distinct'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'motivo' => ['required', 'string', 'max:255'],
        ], [
            'items.required' => 'Indica cuántas piezas se devuelven.',
            'motivo.required' => 'El motivo es obligatorio.',
        ]);

        $cantidades = collect($data['items'])->pluck('cantidad', 'id_detalle')->all();
        $this->reembolsar($venta, $cantidades, Devolucion::DEVOLUCION, $data['motivo'], $this->usuario($request));

        // refresh(): reembolsar() trabajó sobre otra instancia (la bloqueada); recargamos esta
        return $this->show($request, $venta->refresh());
    }

    /** Cancelación total: se reembolsa todo lo que no se había devuelto y la venta queda cancelada. */
    public function cancelar(Request $request, Venta $venta)
    {
        $this->asegurarPropia($request, $venta);

        $data = $request->validate([
            'motivo' => ['required', 'string', 'max:255'],
        ], [
            'motivo.required' => 'El motivo es obligatorio.',
        ]);

        $this->reembolsar($venta, null, Devolucion::CANCELACION, $data['motivo'], $this->usuario($request));

        return $this->show($request, $venta->refresh());
    }

    /**
     * Registra el reembolso y regresa las piezas al inventario de la sucursal de la venta.
     *
     * @param  array<int, int>|null  $cantidades  [id_detalle => piezas]; null = todo lo pendiente (cancelación)
     */
    private function reembolsar(Venta $venta, ?array $cantidades, string $tipo, string $motivo, User $usuario): void
    {
        DB::transaction(function () use ($venta, $cantidades, $tipo, $motivo, $usuario) {
            // Bloquea la venta: dos gerentes no pueden devolver las mismas piezas al mismo tiempo
            $venta = Venta::whereKey($venta->id_venta)->lockForUpdate()->firstOrFail();
            abort_if($venta->estaCancelada(), 422, 'La venta ya está cancelada.');

            $detalles = $venta->detalles()->get()->keyBy('id_detalle');
            $devoluciones = $venta->devoluciones()->get();

            if ($cantidades === null) {
                $cantidades = [];
                foreach ($detalles as $id => $d) {
                    $cantidades[$id] = $d->cantidad - (int) $devoluciones->where('id_detalle', $id)->sum('cantidad');
                }
                $cantidades = array_filter($cantidades);
            }

            foreach ($cantidades as $idDetalle => $cantidad) {
                $cantidad = (int) $cantidad;
                $detalle = $detalles->get($idDetalle);
                abort_if(! $detalle, 422, 'Una de las líneas no pertenece a esta venta.');

                $previas = $devoluciones->where('id_detalle', $idDetalle);
                $disponible = $detalle->cantidad - (int) $previas->sum('cantidad');
                abort_if($cantidad > $disponible, 422,
                    "Solo quedan {$disponible} pieza(s) por devolver de esa línea.");

                // Si se devuelve lo último de la línea, el monto es exactamente lo que falta
                // (evita que el redondeo de varias devoluciones parciales deje centavos sueltos)
                $monto = $cantidad === $disponible
                    ? round((float) $detalle->subtotal - (float) $previas->sum(fn ($d) => (float) $d->monto), 2)
                    : round((float) $detalle->precio_unitario * $cantidad, 2);

                Devolucion::create([
                    'fecha' => now(),
                    'id_venta' => $venta->id_venta,
                    'id_detalle' => $idDetalle,
                    'id_producto' => $detalle->id_producto,
                    'cantidad' => $cantidad,
                    'monto' => $monto,
                    'tipo' => $tipo,
                    'motivo' => $motivo,
                    'id_usuario' => $usuario->id_usuario,
                ]);

                $this->regresarAlInventario($venta->id_sucursal, $detalle->id_producto, $cantidad);
            }

            if ($tipo === Devolucion::CANCELACION) {
                $venta->update([
                    'estado' => Venta::CANCELADA,
                    'fecha_cancelacion' => now(),
                    'id_usuario_cancela' => $usuario->id_usuario,
                    'motivo_cancelacion' => $motivo,
                ]);
            }
        });
    }

    private function regresarAlInventario(int $idSucursal, int $idProducto, int $cantidad): void
    {
        $inventario = Inventario::where('id_sucursal', $idSucursal)
            ->where('id_producto', $idProducto)
            ->lockForUpdate()
            ->first();

        if ($inventario) {
            $inventario->increment('existencias', $cantidad);
        } else {
            Inventario::create([
                'id_producto' => $idProducto,
                'id_sucursal' => $idSucursal,
                'existencias' => $cantidad,
                'stock_minimo' => 5,
            ]);
        }
    }

    /** 404 si la venta es de otra sucursal. */
    private function asegurarPropia(Request $request, Venta $venta): void
    {
        abort_if((int) $venta->id_sucursal !== $this->idSucursal($request), 404);
    }
}
