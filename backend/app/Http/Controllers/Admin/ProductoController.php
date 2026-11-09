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
}
