<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Concerns\UsaSucursalDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    use UsaSucursalDelUsuario;

    public function index(Request $request)
    {
        $idSucursal = $this->idSucursal($request);

        return Compra::with('producto')
            ->where('id_sucursal', $idSucursal)
            ->orderByDesc('fecha')
            ->orderByDesc('id_compra')
            ->get()
            ->map(fn (Compra $c) => [
                'id' => $c->id_compra,
                'fecha' => $c->fecha?->toIso8601String(),
                'producto' => $c->producto->nombre ?? 'Producto eliminado',
                'cantidad' => $c->cantidad,
                'costo' => (float) $c->costo_unitario,
                'total' => (float) $c->total,
                'proveedor' => $c->proveedor,
            ]);
    }

    /** Registra la compra y SUMA la cantidad al inventario de la sucursal, en una sola transacción. */
    public function store(Request $request)
    {
        $idSucursal = $this->idSucursal($request);

        $data = $request->validate([
            'id_producto' => ['required', 'integer', 'exists:producto,id_producto'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'costo_unitario' => ['required', 'numeric', 'min:0'],
            'proveedor' => ['nullable', 'string', 'max:150'],
        ]);

        $compra = DB::transaction(function () use ($data, $idSucursal, $request) {
            $compra = Compra::create([
                'fecha' => now(),
                'id_producto' => $data['id_producto'],
                'id_sucursal' => $idSucursal,
                'id_usuario' => $this->usuario($request)->id_usuario,
                'cantidad' => $data['cantidad'],
                'costo_unitario' => $data['costo_unitario'],
                'total' => round($data['cantidad'] * $data['costo_unitario'], 2),
                'proveedor' => $data['proveedor'] ?? null,
            ]);

            $inventario = Inventario::where('id_producto', $data['id_producto'])
                ->where('id_sucursal', $idSucursal)
                ->lockForUpdate()
                ->first();

            if ($inventario) {
                $inventario->increment('existencias', $data['cantidad']);
            } else {
                Inventario::create([
                    'id_producto' => $data['id_producto'],
                    'id_sucursal' => $idSucursal,
                    'existencias' => $data['cantidad'],
                    'stock_minimo' => 5,
                ]);
            }

            return $compra;
        });

        return response()->json($compra, 201);
    }
}
