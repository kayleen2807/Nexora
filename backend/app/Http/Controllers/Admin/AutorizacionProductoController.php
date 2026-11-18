<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * "Productos por autorizar" (decisión de Kay 2026-11-18): los productos que dan de alta los gerentes
 * llegan aquí. Autorizar = el producto pasa a TODAS las sucursales del negocio (activo, stock 0);
 * rechazar = se queda solo en la sucursal que lo dio de alta.
 */
class AutorizacionProductoController extends Controller
{
    use UsaNegocioDelUsuario;

    /** Solicitudes del negocio: primero las pendientes (más antiguas primero), luego las ya decididas. */
    public function index(Request $request)
    {
        $idNegocio = $this->idNegocio($request);

        $productos = Producto::with(['sucursalOrigen', 'solicita'])
            ->where('id_negocio', $idNegocio)
            ->whereNotNull('autorizacion')
            ->orderByRaw('case when autorizacion = ? then 0 else 1 end', [Producto::PENDIENTE])
            ->orderByRaw('case when autorizacion = ? then fecha_solicitud end asc', [Producto::PENDIENTE])
            ->orderByDesc('fecha_autorizacion')
            ->orderByDesc('id_producto')
            ->get();

        // Precio, IVA, categoría y stock con los que lo tiene HOY la sucursal de origen (una sola consulta)
        $origen = Inventario::with('categoria')
            ->whereIn('id_producto', $productos->pluck('id_producto'))
            ->whereIn('id_sucursal', $productos->pluck('id_sucursal_origen')->filter()->unique())
            ->get()
            ->keyBy(fn (Inventario $i) => $i->id_producto.'-'.$i->id_sucursal);

        return response()->json([
            'pendientes' => $productos->where('autorizacion', Producto::PENDIENTE)->count(),
            'solicitudes' => $productos->map(function (Producto $p) use ($origen) {
                $inv = $origen->get($p->id_producto.'-'.$p->id_sucursal_origen);
                $precio = (float) ($inv?->precio ?? $p->precio);
                $iva = (float) ($inv?->iva ?? $p->iva);

                return [
                    'id' => $p->id_producto,
                    'nombre' => $p->nombre,
                    'foto' => $p->foto ? Storage::disk('public')->url($p->foto) : null,
                    'sucursal' => $p->sucursalOrigen?->nombre,
                    'gerente' => $p->solicita ? trim($p->solicita->nombre.' '.$p->solicita->apellido) : null,
                    'precio' => $precio,
                    'iva' => $iva,
                    'precio_final' => Producto::calcularPrecioFinal($precio, $iva),
                    'categoria' => $inv?->categoria?->nombre,
                    'stock' => (int) ($inv?->existencias ?? 0),
                    'autorizacion' => $p->autorizacion,
                    'motivo_rechazo' => $p->motivo_rechazo,
                    'fecha_solicitud' => $p->fecha_solicitud?->toIso8601String(),
                    'fecha_autorizacion' => $p->fecha_autorizacion?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function autorizar(Request $request, Producto $producto)
    {
        $this->asegurarDelNegocio($request, $producto);
        $producto->autorizar();

        return response()->json(['message' => "\"{$producto->nombre}\" ya está en todas tus sucursales."]);
    }

    public function rechazar(Request $request, Producto $producto)
    {
        $this->asegurarDelNegocio($request, $producto);

        $data = $request->validate([
            'motivo' => ['nullable', 'string', 'max:255'],
        ], [
            'motivo.max' => 'El motivo no puede tener más de 255 caracteres.',
        ]);

        $motivo = trim((string) ($data['motivo'] ?? ''));
        $producto->rechazar($motivo === '' ? null : $motivo);

        return response()->json(['message' => "\"{$producto->nombre}\" se queda solo en la sucursal que lo dio de alta."]);
    }
}
