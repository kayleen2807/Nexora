<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    use UsaNegocioDelUsuario;

    /** Lista los productos del catálogo DEL NEGOCIO con su foto. */
    public function index(Request $request)
    {
        return Producto::where('id_negocio', $this->idNegocio($request))
            ->orderBy('id_producto')->get()->map(fn (Producto $p) => [
            'id' => $p->id_producto,
            'nombre' => $p->nombre,
            'foto' => $p->foto ? Storage::disk('public')->url($p->foto) : null,
            'precio' => (float) $p->precio,
            'iva' => (float) $p->iva,
            'activo' => (bool) $p->estado,
        ]);
    }

    /** Cambia el nombre del producto en el catálogo del negocio (se ve igual en todas sus sucursales). */
    public function update(Request $request, Producto $producto)
    {
        $this->asegurarDelNegocio($request, $producto);

        $request->merge(['nombre' => trim((string) $request->input('nombre'))]);
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
        ], [
            'nombre.required' => 'Escribe el nombre del producto.',
            'nombre.max' => 'El nombre no puede tener más de 150 caracteres.',
        ]);

        $producto->update(['nombre' => $data['nombre']]);

        return response()->json(['id' => $producto->id_producto, 'nombre' => $producto->nombre]);
    }

    /** Sube o reemplaza la foto de un producto del catálogo global. */
    public function uploadFoto(Request $request, Producto $producto)
    {
        $this->asegurarDelNegocio($request, $producto);

        $request->validate([
            'foto' => ['required', 'image', 'max:2048'],
        ]);

        if ($producto->foto) {
            Storage::disk('public')->delete($producto->foto);
        }

        $ruta = $request->file('foto')->store('productos', 'public');
        $producto->update(['foto' => $ruta]);

        return response()->json(['foto' => Storage::disk('public')->url($ruta)]);
    }

    /**
     * Elimina el producto del catálogo del negocio (todas sus sucursales) si no tiene ventas,
     * compras ni devoluciones; si las tiene → 422 con la sugerencia de darlo de baja.
     */
    public function destroy(Request $request, Producto $producto)
    {
        $this->asegurarDelNegocio($request, $producto);

        $motivo = $producto->eliminarSiSePuede();
        abort_if($motivo !== null, 422, $motivo);

        return response()->json(['message' => 'Producto eliminado del catálogo.']);
    }
}
