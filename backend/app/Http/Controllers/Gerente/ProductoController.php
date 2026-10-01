<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Concerns\UsaSucursalDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * El catálogo (`producto`) es compartido, pero precio, IVA y activo/inactivo
 * son DE CADA SUCURSAL (columnas de `inventario`). Un gerente nunca modifica
 * lo que ven las demás sucursales.
 */
class ProductoController extends Controller
{
    use UsaSucursalDelUsuario;

    /**
     * Catálogo con los valores de la sucursal del gerente. Un solo query con LEFT JOIN:
     * si el producto aún no está en su inventario, se muestran los valores del catálogo y stock 0.
     */
    public function index(Request $request)
    {
        $idSucursal = $this->idSucursal($request);

        return Producto::query()
            ->leftJoin('inventario', function ($join) use ($idSucursal) {
                $join->on('inventario.id_producto', '=', 'producto.id_producto')
                    ->where('inventario.id_sucursal', '=', $idSucursal);
            })
            ->orderBy('producto.id_producto')
            ->get([
                'producto.*',
                'inventario.id_inventario',
                'inventario.existencias',
                'inventario.stock_minimo',
                'inventario.precio as precio_sucursal',
                'inventario.iva as iva_sucursal',
                'inventario.estado as estado_sucursal',
            ])
            ->map(function (Producto $p) {
                $enSucursal = $p->getAttribute('id_inventario') !== null;

                return [
                    'id' => $p->id_producto,
                    'nombre' => $p->nombre,
                    'precio' => (float) ($p->getAttribute('precio_sucursal') ?? $p->precio),
                    'iva' => (float) ($p->getAttribute('iva_sucursal') ?? $p->iva),
                    // Si el producto aún no está en la sucursal, hereda el estado del catálogo
                    'activo' => $enSucursal ? (bool) $p->getAttribute('estado_sucursal') : $p->estado,
                    'stock' => (int) ($p->getAttribute('existencias') ?? 0),
                    'stock_minimo' => (int) ($p->getAttribute('stock_minimo') ?? 5),
                ];
            });
    }

    public function store(Request $request)
    {
        $idSucursal = $this->idSucursal($request);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'precio' => ['required', 'numeric', 'min:0'],
            'iva' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'stock' => ['nullable', 'integer', 'min:0'],
        ]);

        // Producto (catálogo, valores por defecto) + su inventario en esta sucursal: los dos o ninguno
        $producto = DB::transaction(function () use ($data, $idSucursal) {
            $producto = Producto::create([
                'nombre' => $data['nombre'],
                'precio' => $data['precio'],
                'iva' => $data['iva'] ?? 16,
                'estado' => true,
            ]);

            Inventario::create([
                'id_producto' => $producto->id_producto,
                'id_sucursal' => $idSucursal,
                'existencias' => $data['stock'] ?? 0,
                'stock_minimo' => 5,
                'precio' => $producto->precio,
                'iva' => $producto->iva,
                'estado' => true,
            ]);

            return $producto;
        });

        return response()->json($producto, 201);
    }

    /** Cambia precio e IVA SOLO en la sucursal del gerente. */
    public function update(Request $request, Producto $producto)
    {
        $idSucursal = $this->idSucursal($request);

        $data = $request->validate([
            'precio' => ['required', 'numeric', 'min:0'],
            'iva' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $inventario = DB::transaction(function () use ($producto, $idSucursal, $data) {
            $inventario = $this->filaDeSucursal($producto, $idSucursal);
            $inventario->fill(['precio' => $data['precio'], 'iva' => $data['iva']])->save();

            return $inventario;
        });

        return response()->json($inventario);
    }

    /** Activa/desactiva el producto SOLO en la sucursal del gerente. */
    public function toggleActivo(Request $request, Producto $producto)
    {
        $idSucursal = $this->idSucursal($request);

        $inventario = DB::transaction(function () use ($producto, $idSucursal) {
            $inventario = $this->filaDeSucursal($producto, $idSucursal);
            $inventario->estado = ! $inventario->estado;
            $inventario->save();

            return $inventario;
        });

        return response()->json($inventario);
    }

    /**
     * Fila de inventario del producto en la sucursal (bloqueada hasta el commit).
     * Si el producto aún no estaba en la sucursal, la prepara con stock 0 y los
     * valores del catálogo (se guarda con el ->save() de quien la llama).
     */
    private function filaDeSucursal(Producto $producto, int $idSucursal): Inventario
    {
        return Inventario::where('id_producto', $producto->id_producto)
            ->where('id_sucursal', $idSucursal)
            ->lockForUpdate()
            ->first()
            ?? new Inventario([
                'id_producto' => $producto->id_producto,
                'id_sucursal' => $idSucursal,
                'existencias' => 0,
                'stock_minimo' => 5,
                'estado' => $producto->estado,
            ]);
    }
}
