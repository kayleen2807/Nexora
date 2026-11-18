<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UsaNegocioDelUsuario;
use App\Http\Controllers\Controller;
use App\Models\Producto;
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

    /**
     * Solo las sucursales del negocio del admin. `id_gerente` = el gerente que de verdad trabaja ahí:
     * a los gerentes creados desde "Crear usuarios" o "Asignar roles" antes del 2026-11-17 no se les
     * guardaba como encargado, y la tabla los mostraba "Sin asignar" (al guardar la sucursal los sacaba).
     */
    public function index(Request $request){
        $idNegocio = $this->idNegocio($request);
        $gerentePorSucursal = User::where('id_negocio', $idNegocio)
            ->where('id_rol', Rol::idDe('gerente'))
            ->whereNotNull('id_sucursal')
            ->orderBy('id_usuario')
            ->pluck('id_usuario', 'id_sucursal');

        return Sucursal::where('id_negocio', $idNegocio)
            ->orderBy('id_sucursal')
            ->get()
            ->each(function (Sucursal $s) use ($gerentePorSucursal) {
                $s->id_gerente ??= $gerentePorSucursal[$s->id_sucursal] ?? null;
            });
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

        // La sucursal nace con los productos que el admin autorizó para todo el negocio (activos, stock 0)
        $sucursal = DB::transaction(function () use ($data) {
            $sucursal = Sucursal::create($data);
            Producto::agregarAutorizadosA($sucursal);

            return $sucursal;
        });

        return response()->json($sucursal, 201);
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
            Sucursal::bloquear($sucursal->id_sucursal);

            if (! array_key_exists('id_gerente', $data)) {
                $sucursal->update($data);

                return;
            }

            $nuevo = $data['id_gerente'] ? (int) $data['id_gerente'] : null;

            if ($nuevo) {
                // Un gerente = una sucursal: si ya trabaja en OTRA, no se mueve en silencio (decisión de Kay)
                $gerente = User::whereKey($nuevo)->lockForUpdate()->first();
                if ($gerente->id_sucursal && (int) $gerente->id_sucursal !== (int) $sucursal->id_sucursal) {
                    $otra = Sucursal::find($gerente->id_sucursal)?->nombre ?? 'otra sucursal';
                    abort(422, trim($gerente->nombre.' '.$gerente->apellido).' ya es gerente de '.$otra
                        .'. Quítalo de ahí primero (en Sucursales o en Asignar roles) y luego asígnalo aquí.');
                }
            }

            $sucursal->update($data);

            // Una sucursal = un gerente: quien la encabezaba (o trabajaba ahí como gerente) queda sin sucursal
            $quitados = User::where('id_sucursal', $sucursal->id_sucursal)
                ->where('id_rol', Rol::idDe('gerente'))
                ->when($nuevo, fn ($q) => $q->where('id_usuario', '!=', $nuevo))
                ->update(['id_sucursal' => null]);
            if ($quitados) {
                $aviso = 'El gerente anterior quedó sin sucursal asignada.';
            }

            if ($nuevo) {
                User::whereKey($nuevo)->update(['id_sucursal' => $sucursal->id_sucursal]);
                Sucursal::ponerEncargado($sucursal->id_sucursal, $nuevo);
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
