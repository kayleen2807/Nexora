<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Concerns\UsaSucursalDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\CorteCaja;
use App\Models\DetalleVenta;
use App\Models\Inventario;
use App\Models\MetodoPago;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    use UsaSucursalDelUsuario;

    /** Productos activos EN ESTA SUCURSAL, con el precio final (IVA incluido) de esta sucursal. */
    public function productos(Request $request)
    {
        $idSucursal = $this->idSucursal($request);

        return Inventario::with('producto')
            ->where('id_sucursal', $idSucursal)
            ->where('estado', true)
            ->get()
            ->filter(fn (Inventario $i) => $i->producto !== null)
            ->sortBy(fn (Inventario $i) => $i->producto->nombre)
            ->values()
            ->map(fn (Inventario $i) => [
                'id' => $i->id_producto,
                'nombre' => $i->producto->nombre,
                'precio' => $i->precioFinal($i->producto),
                'stock' => $i->existencias,
            ]);
    }

    /**
     * Registra una venta completa. Los precios se recalculan AQUÍ con los datos de la
     * BD: nunca se confía en los precios que mande el navegador.
     */
    public function store(Request $request)
    {
        $idSucursal = $this->idSucursal($request);
        $usuario = $this->usuario($request);

        $data = $request->validate([
            'id_metodo_pago' => ['required', 'integer', 'exists:metodo_pago,id_metodo_pago'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_producto' => ['required', 'integer', 'distinct'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'recibido' => ['nullable', 'numeric', 'min:0'],
        ], [
            'items.required' => 'El carrito está vacío.',
        ]);

        $turno = CorteCaja::abiertoDe($usuario->id_usuario);
        abort_if(! $turno, 422, 'Abre tu caja antes de vender.');
        abort_if((int) $turno->caja?->id_sucursal !== $idSucursal, 422, 'Tu turno abierto es de otra sucursal.');

        $metodo = MetodoPago::findOrFail($data['id_metodo_pago']);
        $cantidades = collect($data['items'])->pluck('cantidad', 'id_producto');

        $venta = DB::transaction(function () use ($cantidades, $idSucursal, $usuario, $turno, $metodo, $data) {
            $productos = Producto::whereIn('id_producto', $cantidades->keys())->get()->keyBy('id_producto');

            // Bloquea las filas de inventario (en orden fijo, para evitar deadlocks) hasta el commit:
            // dos cajeros no pueden vender la misma última pieza
            $inventarios = Inventario::where('id_sucursal', $idSucursal)
                ->whereIn('id_producto', $cantidades->keys())
                ->orderBy('id_producto')
                ->lockForUpdate()
                ->get()
                ->keyBy('id_producto');

            $subtotal = 0.0;
            $total = 0.0;
            $lineas = [];

            foreach ($cantidades as $idProducto => $cantidad) {
                $producto = $productos->get($idProducto);
                $inventario = $inventarios->get($idProducto);

                abort_if(! $producto || ! $inventario || ! $inventario->estado, 422, 'Un producto del carrito ya no está disponible.');
                abort_if($inventario->existencias < $cantidad, 422,
                    "Stock insuficiente de {$producto->nombre} (disponible: {$inventario->existencias}).");

                // Precio e IVA de ESTA sucursal (o los del catálogo si la sucursal no los cambió)
                $precioUnitario = $inventario->precioFinal($producto);
                $importe = round($precioUnitario * $cantidad, 2);

                $subtotal += round($inventario->precioBase($producto) * $cantidad, 2);
                $total += $importe;
                $lineas[] = compact('producto', 'inventario', 'cantidad', 'precioUnitario', 'importe');
            }

            $total = round($total, 2);
            $subtotal = round($subtotal, 2);

            if (strtolower($metodo->nombre) === 'efectivo') {
                abort_if(($data['recibido'] ?? 0) < $total, 422, 'El efectivo recibido no cubre el total.');
            }

            $venta = Venta::create([
                'fecha_hora' => now(),
                'subtotal' => $subtotal,
                'total' => $total,
                'id_sucursal' => $idSucursal,
                'id_cajero' => $usuario->id_usuario,
                'id_metodo_pago' => $metodo->id_metodo_pago,
                'id_caja' => $turno->id_caja,
            ]);

            foreach ($lineas as $l) {
                DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                    'id_producto' => $l['producto']->id_producto,
                    'cantidad' => $l['cantidad'],
                    'precio_unitario' => $l['precioUnitario'],
                    'subtotal' => $l['importe'],
                ]);

                $l['inventario']->decrement('existencias', $l['cantidad']);
            }

            return $venta;
        });

        $venta->load('detalles.producto');
        $recibido = isset($data['recibido']) ? round((float) $data['recibido'], 2) : null;
        $esEfectivo = strtolower($metodo->nombre) === 'efectivo';

        // Datos para imprimir el ticket
        return response()->json([
            'folio' => $venta->id_venta,
            'fecha' => $venta->fecha_hora->toIso8601String(),
            'sucursal' => $usuario->sucursal->nombre ?? '',
            'cajero' => trim($usuario->nombre.' '.$usuario->apellido),
            'caja' => $turno->caja?->numero_caja,
            'items' => $venta->detalles->map(fn (DetalleVenta $d) => [
                'nombre' => $d->producto->nombre ?? '',
                'cantidad' => $d->cantidad,
                'precio' => (float) $d->precio_unitario,
            ]),
            'subtotal' => (float) $venta->subtotal,
            'iva' => round((float) $venta->total - (float) $venta->subtotal, 2),
            'total' => (float) $venta->total,
            'metodo' => $esEfectivo ? 'efectivo' : 'tarjeta',
            'recibido' => $esEfectivo ? $recibido : null,
            'cambio' => $esEfectivo ? round($recibido - (float) $venta->total, 2) : null,
        ], 201);
    }
}
