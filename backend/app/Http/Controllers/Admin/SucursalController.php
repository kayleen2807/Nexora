<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SucursalController extends Controller
{
    use UsaNegocioDelUsuario;

    /** Solo las sucursales del negocio del admin. */
    public function index(Request $request){
        return Sucursal::where('id_negocio', $this->idNegocio($request))
            ->orderBy('id_sucursal')
            ->get();
    }

    public function store(Request $request){
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'direccion' => ['required', 'string'],
            'contacto' => ['nullable', 'digits:10'],
            'estado' => ['nullable', 'string', 'max:20'],
        ], [
            'contacto.digits' => __('Contact must contain exactly 10 digits.'),
        ]);

        $data['id_negocio'] = $this->idNegocio($request);

        return response()->json(Sucursal::create($data), 201);
    }

    public function update(Request $request, Sucursal $sucursal){
        $this->asegurarDelNegocio($request, $sucursal);
        $idNegocio = $this->idNegocio($request);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'direccion' => ['required', 'string'],
            'contacto' => ['nullable', 'digits:10'],
            'estado' => ['nullable', 'string', 'max:20'],
            // Solo un usuario con rol Gerente y del mismo negocio puede encargarse de la sucursal
            'id_gerente' => ['nullable', 'integer', Rule::exists('usuario', 'id_usuario')
                ->where('id_negocio', $idNegocio)
                ->where('id_rol', Rol::idDe('gerente'))],
        ], [
            'contacto.digits' => __('Contact must contain exactly 10 digits.'),
            'id_gerente.exists' => 'Solo puedes asignar como encargado a un usuario con rol Gerente de tu negocio.',
        ]);

        $aviso = null;

        DB::transaction(function () use ($sucursal, $data, &$aviso) {
            $anterior = $sucursal->id_gerente;
            $sucursal->update($data);

            if (! array_key_exists('id_gerente', $data)) {
                return;
            }

            $nuevo = $data['id_gerente'];

            // El gerente anterior deja de pertenecer a esta sucursal (no se queda "colgado")
            if ($anterior && (int) $anterior !== (int) $nuevo) {
                $quitado = User::where('id_usuario', $anterior)
                    ->where('id_sucursal', $sucursal->id_sucursal)
                    ->update(['id_sucursal' => null]);
                if ($quitado) {
                    $aviso = 'El gerente anterior quedó sin sucursal asignada.';
                }
            }

            if ($nuevo) {
                // Si el nuevo gerente encabezaba otra sucursal, esa sucursal queda sin gerente
                Sucursal::where('id_gerente', $nuevo)
                    ->where('id_sucursal', '!=', $sucursal->id_sucursal)
                    ->update(['id_gerente' => null]);

                User::where('id_usuario', $nuevo)->update(['id_sucursal' => $sucursal->id_sucursal]);
            }
        });

        return response()->json([...$sucursal->fresh()->toArray(), 'aviso' => $aviso]);
    }

    public function destroy(Request $request, Sucursal $sucursal){
        $this->asegurarDelNegocio($request, $sucursal);

        try {
            DB::transaction(function () use ($sucursal) {
                User::where('id_sucursal', $sucursal->id_sucursal)
                    ->update(['id_sucursal' => null]);

                $sucursal->delete();
            });
        } catch (QueryException $e) {
            // FK desde caja / inventario / venta: la sucursal ya tiene historial
            return response()->json([
                'message' => 'No se puede eliminar: la sucursal tiene cajas, inventario o ventas registrados. Cámbiala a "Inactiva" en su lugar.',
            ], 422);
        }

        return response()->noContent();
    }
}
