<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Concerns\UsaSucursalDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    use UsaSucursalDelUsuario;

    /**
     * Suma (o resta, si es negativo) `ajuste` a las existencias del producto en la
     * sucursal del gerente. Si el producto aún no tenía fila de inventario aquí, se crea.
     */
    public function ajustar(Request $request, Producto $producto)
    {
        $idSucursal = $this->idSucursal($request);

        $data = $request->validate([
            'ajuste' => ['required', 'integer', 'not_in:0'],
        ]);

        $inventario = DB::transaction(function () use ($producto, $idSucursal, $data) {
            // lockForUpdate: si un cajero vende este producto al mismo tiempo, espera a que terminemos
            $inventario = Inventario::where('id_producto', $producto->id_producto)
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

            $nuevo = $inventario->existencias + $data['ajuste'];

            abort_if($nuevo < 0, 422, "No puedes dejar el stock en negativo (actual: {$inventario->existencias}).");

            $inventario->existencias = $nuevo;
            $inventario->save();

            return $inventario;
        });

        return response()->json([
            'id' => $producto->id_producto,
            'stock' => $inventario->existencias,
        ]);
    }
}
