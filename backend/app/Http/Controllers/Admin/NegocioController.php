<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Negocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Datos del negocio del admin y sus otros negocios.
 *
 * Un dueño puede tener varios negocios (negocio.id_propietario no es único). El negocio con el
 * que trabaja en este momento es usuario.id_negocio: todo el panel (trait UsaNegocioDelUsuario)
 * filtra por él, así que "cambiar de negocio" es solo cambiar ese valor.
 */
class NegocioController extends Controller
{
    use UsaNegocioDelUsuario;

    /** Tope de negocios por dueño (evita crear cientos por error o por abuso). */
    private const MAX_NEGOCIOS = 10;

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

    /** GET /administrador/negocios — los negocios de los que es dueño, marcando el activo. */
    public function index(Request $request)
    {
        $usuario = $this->usuario($request);

        return Negocio::where('id_propietario', $usuario->id_usuario)
            ->orderBy('nombre')
            ->get()
            ->map(fn (Negocio $n) => [
                'id' => $n->id_negocio,
                'nombre' => $n->nombre,
                'activo' => $n->id_negocio === (int) $usuario->id_negocio,
            ]);
    }

    /** POST /administrador/negocios — crea otro negocio (vacío) y el dueño pasa a trabajar en él. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
        ], [
            'nombre.required' => 'Escribe el nombre del nuevo negocio.',
            'nombre.max' => 'El nombre puede tener máximo 150 caracteres.',
        ]);

        $usuario = $this->usuario($request);
        $nombre = trim($data['nombre']);
        $suyos = Negocio::where('id_propietario', $usuario->id_usuario);

        abort_if((clone $suyos)->count() >= self::MAX_NEGOCIOS, 422,
            'Llegaste al máximo de '.self::MAX_NEGOCIOS.' negocios.');
        // Dos negocios con el mismo nombre se confundirían en el selector
        abort_if((clone $suyos)->whereRaw('lower(nombre) = ?', [mb_strtolower($nombre)])->exists(), 422,
            'Ya tienes un negocio con ese nombre.');

        // Crear el negocio y cambiarse a él van juntos: o las dos cosas o ninguna
        $negocio = DB::transaction(function () use ($usuario, $nombre) {
            $negocio = Negocio::create(['nombre' => $nombre, 'id_propietario' => $usuario->id_usuario]);
            $usuario->update(['id_negocio' => $negocio->id_negocio]);

            return $negocio;
        });

        return response()->json(['id' => $negocio->id_negocio, 'nombre' => $negocio->nombre], 201);
    }

    /** PUT /administrador/negocios/{negocio}/activar — cambia el negocio con el que trabaja. */
    public function activar(Request $request, Negocio $negocio)
    {
        $usuario = $this->usuario($request);

        // Solo a un negocio del que sea dueño; si es de otra persona, ni se revela que existe
        abort_if((int) $negocio->id_propietario !== $usuario->id_usuario, 404);

        $usuario->update(['id_negocio' => $negocio->id_negocio]);

        return response()->json(['id' => $negocio->id_negocio, 'nombre' => $negocio->nombre]);
    }
}
