<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /** Lista todos los productos del catálogo con su foto. */
    public function index()
    {
        return Producto::orderBy('id_producto')->get()->map(fn (Producto $p) => [
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
