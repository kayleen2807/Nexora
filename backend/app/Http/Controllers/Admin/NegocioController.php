<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Negocio;
use Illuminate\Http\Request;

/** Datos del negocio del admin (por ahora, su nombre). */
class NegocioController extends Controller
{
    use UsaNegocioDelUsuario;

    public function show(Request $request)
    {
        return Negocio::findOrFail($this->idNegocio($request));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
        ], [
            'nombre.required' => 'Escribe el nombre de tu negocio.',
        ]);

        $negocio = Negocio::findOrFail($this->idNegocio($request));
        $negocio->update(['nombre' => trim($data['nombre'])]);

        return $negocio;
    }
}
