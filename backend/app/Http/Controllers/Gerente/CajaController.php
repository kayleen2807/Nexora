<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Concerns\UsaSucursalDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Caja;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CajaController extends Controller
{
    use UsaSucursalDelUsuario;

    public function index(Request $request)
    {
        $idSucursal = $this->idSucursal($request);

        return Caja::with('corteAbierto.usuario')
            ->where('id_sucursal', $idSucursal)
            ->orderBy('numero_caja')
            ->get()
            ->map(fn (Caja $c) => [
                'id' => $c->id_caja,
                'numero' => $c->numero_caja,
                'activa' => $c->estado !== Caja::INACTIVA,
                // "En turno": un cajero la abrió y aún no hace su corte
                'en_turno' => $c->corteAbierto !== null,
                'cajero' => $c->corteAbierto?->usuario
                    ? trim($c->corteAbierto->usuario->nombre.' '.$c->corteAbierto->usuario->apellido)
                    : null,
            ]);
    }

    public function store(Request $request)
    {
        $idSucursal = $this->idSucursal($request);

        $data = $request->validate([
            'numero' => [
                'required', 'integer', 'min:1',
                // El número de caja no se repite dentro de la misma sucursal
                Rule::unique('caja', 'numero_caja')->where('id_sucursal', $idSucursal),
            ],
        ], [
            'numero.unique' => 'Ya existe una caja con ese número en tu sucursal.',
        ]);

        $caja = Caja::create([
            'numero_caja' => $data['numero'],
            'estado' => Caja::ACTIVA,
            'id_sucursal' => $idSucursal,
        ]);

        return response()->json($caja, 201);
    }

    public function toggle(Request $request, Caja $caja)
    {
        $this->asegurarPropia($request, $caja);

        $desactivando = $caja->estado !== Caja::INACTIVA;
        abort_if($desactivando && $caja->corteAbierto()->exists(), 422,
            'La caja está en turno; el cajero debe hacer su corte antes de desactivarla.');

        $caja->update(['estado' => $desactivando ? Caja::INACTIVA : Caja::ACTIVA]);

        return response()->json($caja);
    }

    public function destroy(Request $request, Caja $caja)
    {
        $this->asegurarPropia($request, $caja);

        try {
            $caja->delete();
        } catch (QueryException $e) {
            // FK desde venta / corte_caja: la caja ya tiene historial
            return response()->json([
                'message' => 'No se puede eliminar: la caja tiene ventas o cortes registrados. Desactívala en su lugar.',
            ], 422);
        }

        return response()->noContent();
    }

    /** 404 si la caja es de otra sucursal (no revelamos que existe). */
    private function asegurarPropia(Request $request, Caja $caja): void
    {
        abort_if((int) $caja->id_sucursal !== $this->idSucursal($request), 404);
    }
}
