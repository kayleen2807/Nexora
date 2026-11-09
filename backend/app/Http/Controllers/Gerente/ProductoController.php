<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Concerns\UsaSucursalDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\Producto;
use App\Support\Limites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            ->where('producto.id_negocio', $this->idNegocio($request))
            ->leftJoin('inventario', function ($join) use ($idSucursal) {
                $join->on('inventario.id_producto', '=', 'producto.id_producto')
                    ->where('inventario.id_sucursal', '=', $idSucursal);
            })
            // Categoría de ESTA sucursal (vive en inventario.id_categoria)
            ->leftJoin('categoria', 'categoria.id_categoria', '=', 'inventario.id_categoria')
            ->orderBy('producto.id_producto')
            ->get([
                'producto.*',
                'inventario.id_inventario',
                'inventario.existencias',
                'inventario.stock_minimo',
                'inventario.precio as precio_sucursal',
                'inventario.iva as iva_sucursal',
                'inventario.estado as estado_sucursal',
                'categoria.nombre as categoria_sucursal',
            ])
            ->map(function (Producto $p) {
                $enSucursal = $p->getAttribute('id_inventario') !== null;

                return [
                    'id' => $p->id_producto,
                    'nombre' => $p->nombre,
                    'foto' => $p->foto ? Storage::disk('public')->url($p->foto) : null,
                    'precio' => (float) ($p->getAttribute('precio_sucursal') ?? $p->precio),
                    'iva' => (float) ($p->getAttribute('iva_sucursal') ?? $p->iva),
                    // Si el producto aún no está en la sucursal, hereda el estado del catálogo
                    'activo' => $enSucursal ? (bool) $p->getAttribute('estado_sucursal') : $p->estado,
                    'stock' => (int) ($p->getAttribute('existencias') ?? 0),
                    'stock_minimo' => (int) ($p->getAttribute('stock_minimo') ?? 5),
                    'categoria' => $p->getAttribute('categoria_sucursal'),
                ];
            });
    }

    public function store(Request $request)
    {
        $idSucursal = $this->idSucursal($request);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'precio' => ['required', 'numeric', 'min:0', 'max:'.Limites::DINERO],
            'iva' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'stock' => ['nullable', 'integer', 'min:0', 'max:'.Limites::ENTERO],
            'categoria' => ['nullable', 'string', 'max:40'],
        ], $this->mensajes());

        $idNegocio = $this->idNegocio($request);

        // Producto (catálogo del negocio, valores por defecto) + su inventario en esta sucursal: los dos o ninguno
        $producto = DB::transaction(function () use ($data, $idSucursal, $idNegocio) {
            $producto = Producto::create([
                'nombre' => $data['nombre'],
                'precio' => $data['precio'],
                'iva' => $data['iva'] ?? 16,
                'estado' => true,
                'id_negocio' => $idNegocio,
            ]);

            Inventario::create([
                'id_producto' => $producto->id_producto,
                'id_sucursal' => $idSucursal,
                'existencias' => $data['stock'] ?? 0,
                'stock_minimo' => 5,
                'precio' => $producto->precio,
                'iva' => $producto->iva,
                'estado' => true,
                'id_categoria' => Categoria::idPara($data['categoria'] ?? null, $idSucursal),
            ]);

            return $producto;
        });

        return response()->json($producto, 201);
    }

    /** Sube o reemplaza la foto del producto (catálogo global). */
    public function uploadFoto(Request $request, Producto $producto)
    {
        $this->asegurarDelNegocio($request, $producto);

        $request->validate([
            'foto' => ['required', 'image', 'max:2048'],
        ]);

        // Borra la foto anterior si existe
        if ($producto->foto) {
            Storage::disk('public')->delete($producto->foto);
        }

        $ruta = $request->file('foto')->store('productos', 'public');
        $producto->update(['foto' => $ruta]);

        return response()->json(['foto' => Storage::disk('public')->url($ruta)]);
    }

    /** Cambia precio, IVA y categoría SOLO en la sucursal del gerente. */
    public function update(Request $request, Producto $producto)
    {
        $this->asegurarDelNegocio($request, $producto);
        $idSucursal = $this->idSucursal($request);

        $data = $request->validate([
            'precio' => ['required', 'numeric', 'min:0', 'max:'.Limites::DINERO],
            'iva' => ['required', 'numeric', 'min:0', 'max:100'],
            'categoria' => ['nullable', 'string', 'max:40'],
        ], $this->mensajes());

        $inventario = DB::transaction(function () use ($producto, $idSucursal, $data, $request) {
            $inventario = $this->filaDeSucursal($producto, $idSucursal);
            $inventario->fill(['precio' => $data['precio'], 'iva' => $data['iva']]);
            // Solo si el formulario la manda: un cliente viejo sin el campo no borra la categoría
            if ($request->has('categoria')) {
                $inventario->id_categoria = Categoria::idPara($data['categoria'] ?? null, $idSucursal);
            }
            $inventario->save();

            return $inventario;
        });

        return response()->json($inventario);
    }

    /** Activa/desactiva el producto SOLO en la sucursal del gerente. */
    public function toggleActivo(Request $request, Producto $producto)
    {
        $this->asegurarDelNegocio($request, $producto);
        $idSucursal = $this->idSucursal($request);

        $inventario = DB::transaction(function () use ($producto, $idSucursal) {
            $inventario = $this->filaDeSucursal($producto, $idSucursal);
            $inventario->estado = ! $inventario->estado;
            $inventario->save();

            return $inventario;
        });

        return response()->json($inventario);
    }

    /** Mensajes en español para los límites (los genéricos de Laravel salen en inglés). */
    private function mensajes(): array
    {
        return [
            'precio.max' => 'El precio no puede ser mayor a '.Limites::dineroTexto().'.',
            'stock.max' => 'El stock no puede ser mayor a '.number_format(Limites::ENTERO).'.',
            'categoria.max' => 'La categoría no puede tener más de 40 caracteres.',
        ];
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
