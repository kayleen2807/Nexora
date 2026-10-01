<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario;

class InventarioController extends Controller
{
    public function index()
    {
        return Inventario::with('producto')->get()->map(fn ($i) => [
            'id_sucursal' => $i->id_sucursal,
            'producto' => $i->producto->nombre ?? 'Producto eliminado',
            'existencias' => $i->existencias,
            'stock_minimo' => $i->stock_minimo,
        ]);
    }
    
}
