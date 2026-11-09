<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Concerns\UsaSucursalDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\Inventario;
use App\Support\Limites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
                'foto' => $c->producto?->foto ? Storage::disk('public')->url($c->producto->foto) : null,
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
            // Solo productos del catálogo de SU negocio
            'id_producto' => ['required', 'integer', Rule::exists('producto', 'id_producto')->where('id_negocio', $this->idNegocio($request))],
            'cantidad' => ['required', 'integer', 'min:1', 'max:'.Limites::ENTERO],
            'costo_unitario' => ['required', 'numeric', 'min:0', 'max:'.Limites::DINERO],
            'proveedor' => ['nullable', 'string', 'max:150'],
        ], [
            'cantidad.max' => 'La cantidad es demasiado grande.',
            'costo_unitario.max' => 'El costo no puede ser mayor a '.Limites::dineroTexto().'.',
        ]);

        // compra.total es numeric(10,2): cantidad × costo debe caber
        abort_if(round($data['cantidad'] * $data['costo_unitario'], 2) > Limites::DINERO, 422,
            'El total de la compra supera el máximo permitido ('.Limites::dineroTexto().').');

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

            abort_if(($inventario->existencias ?? 0) + $data['cantidad'] > Limites::ENTERO, 422,
                'El stock resultante supera el máximo permitido ('.number_format(Limites::ENTERO).').');

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
