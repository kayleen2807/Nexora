<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $idSucursal = $request->user()->id_sucursal;

        return Producto::orderBy('id_producto')->get()->map(function ($p) use ($idSucursal) {
            $inv = Inventario::where('id_producto', $p->id_producto)
                ->where('id_sucursal', $idSucursal)
                ->first();

            return [
                'id'     => $p->id_producto,
                'nombre' => $p->nombre,
                'precio' => (float) $p->precio,
                'iva'    => (float) $p->iva,
                'activo' => (bool) $p->activo,
                'stock'  => $inv->existencias ?? 0,
            ];
        });
    }

    public function store(Request $request)
    {
        $idSucursal = $request->user()->id_sucursal;
        if (! $idSucursal) {
            return response()->json(['message' => 'No tienes una sucursal asignada.'], 422);
        }

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'precio' => ['required', 'numeric', 'min:0'],
            'iva'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'stock'  => ['nullable', 'integer', 'min:0'],
        ]);

        $producto = Producto::create([
            'nombre' => $data['nombre'],
            'precio' => $data['precio'],
            'iva'    => $data['iva'] ?? 16,
            'activo' => true,
        ]);

        Inventario::create([
            'id_producto'  => $producto->id_producto,
            'id_sucursal'  => $idSucursal,
            'existencias'  => $data['stock'] ?? 0,
            'stock_minimo' => 5,
        ]);

        return response()->json($producto, 201);
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'precio' => ['required', 'numeric', 'min:0'],
            'iva'    => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $producto->update($data);
        return response()->json($producto);
    }

    public function toggleActivo(Producto $producto)
    {
        $producto->update(['activo' => ! $producto->activo]);
        return response()->json($producto);
    }
}