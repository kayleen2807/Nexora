<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use Illuminate\Support\Facades\Storage;

class InventarioController extends Controller
{
    public function index()
    {
        return Inventario::with('producto')->get()->map(fn ($i) => [
            'id_sucursal' => $i->id_sucursal,
            'producto' => $i->producto->nombre ?? 'Producto eliminado',
            'foto' => $i->producto?->foto ? Storage::disk('public')->url($i->producto->foto) : null,
            'existencias' => $i->existencias,
            'stock_minimo' => $i->stock_minimo,
        ]);
    }
    
}
