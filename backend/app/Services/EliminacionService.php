<?php

namespace App\Services;

use App\Models\Negocio;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Borrado de cuentas y de negocios (decisión de Kay 2026-11-17).
 *
 * - Empleado (gerente/cajero): si no tiene historial se borra; si tiene ventas, cortes, compras o
 *   devoluciones a su nombre se ANONIMIZA (no puede entrar, su correo queda libre y las cifras cuadran).
 * - Negocio: se borra con TODO lo suyo (sucursales, empleados, productos, ventas, cortes, fotos).
 * - Dueño: se borran todos sus negocios y luego su cuenta.
 *
 * Todo dentro de una transacción; las fotos se borran del disco solo DESPUÉS de que la BD confirmó.
 */
class EliminacionService
{
    /** Devuelve 'eliminada' o 'anonimizada'. */
    public function eliminarEmpleado(User $usuario): string
    {
        return DB::transaction(fn () => $this->borrarOAnonimizar($usuario));
    }

    /** Borra un negocio completo. El dueño queda trabajando en otro de sus negocios (o sin negocio). */
    public function eliminarNegocio(Negocio $negocio): void
    {
        $fotos = DB::transaction(fn () => $this->borrarNegocio($negocio));
        $this->borrarFotos($fotos);
    }

    /** Cuenta del dueño: todos sus negocios y después él. */
    public function eliminarDueno(User $dueno): string
    {
        $fotos = [];
        $resultado = DB::transaction(function () use ($dueno, &$fotos) {
            foreach (Negocio::where('id_propietario', $dueno->id_usuario)->get() as $negocio) {
                array_push($fotos, ...$this->borrarNegocio($negocio));
            }

            return $this->borrarOAnonimizar($dueno->refresh());
        });
        $this->borrarFotos($fotos);

        return $resultado;
    }

    /**
     * Intenta el DELETE dentro de un punto de guardado (transacción anidada): si la BD lo impide por una
     * llave foránea (23503 = tiene ventas, cortes, etc.), se deshace solo ese intento y se anonimiza.
     */
    private function borrarOAnonimizar(User $usuario): string
    {
        Sucursal::where('id_gerente', $usuario->id_usuario)->update(['id_gerente' => null]);
        UserInvitation::where('user_id', $usuario->id_usuario)->delete();
        DB::table('password_reset_tokens')->where('email', $usuario->correo)->delete();

        try {
            DB::transaction(fn () => $usuario->delete());

            return 'eliminada';
        } catch (QueryException $e) {
            if ($e->getCode() !== '23503') {
                throw $e;
            }
        }

        $usuario->forceFill([
            'nombre' => 'Usuario',
            'apellido' => 'eliminado',
            // Correo imposible (dominio .invalid reservado): el real queda libre para registrarse otra vez
            'correo' => 'eliminado.'.$usuario->id_usuario.'.'.Str::lower(Str::random(8)).'@nexora.invalid',
            'contrasena' => Str::random(64),   // el cast 'hashed' la encripta; nadie la conoce
            'remember_token' => null,
            'fecha_nacimiento' => null,
            'id_sucursal' => null,
            'id_negocio' => null,              // ya no aparece en las listas de ningún negocio
            'email_verified_at' => null,
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
            'dos_pasos_activo' => false,
            'dos_pasos_codigo' => null,
            'dos_pasos_expira' => null,
        ])->save();

        return 'anonimizada';
    }

    /**
     * Borra todo lo del negocio en orden de dependencias (primero lo que apunta a otras tablas).
     * Devuelve las rutas de las fotos para borrarlas del disco después del commit.
     */
    private function borrarNegocio(Negocio $negocio): array
    {
        $id = $negocio->id_negocio;
        Negocio::whereKey($id)->lockForUpdate()->first();

        $sucursales = DB::table('sucursal')->where('id_negocio', $id)->pluck('id_sucursal');
        $ventas = DB::table('venta')->select('id_venta')->whereIn('id_sucursal', $sucursales);
        $cajas = DB::table('caja')->select('id_caja')->whereIn('id_sucursal', $sucursales);
        $productos = DB::table('producto')->select('id_producto')->where('id_negocio', $id);
        $fotos = DB::table('producto')->where('id_negocio', $id)->whereNotNull('foto')->pluck('foto')->all();
        $empleados = DB::table('usuario')->where('id_negocio', $id)
            ->where('id_usuario', '!=', $negocio->id_propietario ?? 0)->get(['id_usuario', 'correo']);

        DB::table('devolucion')->whereIn('id_venta', $ventas)->orWhereIn('id_producto', $productos)->delete();
        DB::table('detalle_venta')->whereIn('id_venta', $ventas)->orWhereIn('id_producto', $productos)->delete();
        DB::table('venta')->whereIn('id_sucursal', $sucursales)->delete();
        DB::table('corte_caja')->whereIn('id_caja', $cajas)->delete();
        DB::table('compra')->whereIn('id_sucursal', $sucursales)->orWhereIn('id_producto', $productos)->delete();
        DB::table('inventario')->whereIn('id_sucursal', $sucursales)->orWhereIn('id_producto', $productos)->delete();
        DB::table('caja')->whereIn('id_sucursal', $sucursales)->delete();
        DB::table('producto')->where('id_negocio', $id)->delete();
        DB::table('reporte')->where('id_negocio', $id)->delete();

        // Empleados: ya no tienen nada a su nombre (sus ventas y cortes se borraron arriba)
        DB::table('sucursal')->whereIn('id_sucursal', $sucursales)->update(['id_gerente' => null]);
        UserInvitation::whereIn('user_id', $empleados->pluck('id_usuario'))->delete();
        DB::table('password_reset_tokens')->whereIn('email', $empleados->pluck('correo'))->delete();
        DB::table('usuario')->whereIn('id_usuario', $empleados->pluck('id_usuario'))->delete();
        DB::table('usuario')->whereIn('id_sucursal', $sucursales)->update(['id_sucursal' => null]);

        DB::table('categoria')->whereIn('id_sucursal', $sucursales)->delete();
        DB::table('sucursal')->whereIn('id_sucursal', $sucursales)->delete();

        // Si el dueño trabajaba en este negocio, pasa a otro de los suyos (o a ninguno)
        $otro = Negocio::where('id_propietario', $negocio->id_propietario)->where('id_negocio', '!=', $id)
            ->orderBy('id_negocio')->value('id_negocio');
        DB::table('usuario')->where('id_negocio', $id)->update(['id_negocio' => $otro]);

        DB::table('negocio')->where('id_negocio', $id)->delete();

        return $fotos;
    }

    private function borrarFotos(array $fotos): void
    {
        foreach ($fotos as $foto) {
            Storage::disk('public')->delete($foto);
        }
    }
}
