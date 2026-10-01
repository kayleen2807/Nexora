<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    private const ROLES = [1 => 'admin', 2 => 'gerente', 3 => 'cajero'];
    private const ROLES_INVERSO = ['admin' => 1, 'gerente' => 2, 'cajero' => 3];

    public function index()
    {
        return User::orderBy('id_usuario')->get()->map(fn ($u) => [
            'id' => $u->id_usuario,
            'nombre' => trim($u->nombre . ' ' . $u->apellido),
            'rol' => self::ROLES[$u->id_rol] ?? 'cajero',
            'sucursalId' => $u->id_sucursal,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'apellido' => ['required', 'string', 'max:150'],
            'correo' => ['required', 'email', 'max:150', 'unique:usuario,correo'],
            'contrasena' => ['required', 'string', 'min:6'],
            'rol' => ['required', 'in:admin,gerente,cajero'],
            'id_sucursal' => ['nullable', 'integer', 'exists:sucursal,id_sucursal'],
        ]);

        $usuario = User::create([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'correo' => $data['correo'],
            'contrasena' => $data['contrasena'], // se hashea sola por el cast del modelo
            'id_rol' => self::ROLES_INVERSO[$data['rol']],
            'id_sucursal' => $data['id_sucursal'] ?? null,
        ]);

        return response()->json($usuario, 201);
    }

    public function actualizarRol(Request $request, User $usuario)
    {
        $data = $request->validate([
            'rol' => ['required', 'in:admin,gerente,cajero'],
            'id_sucursal' => ['nullable', 'integer', 'exists:sucursal,id_sucursal'],
        ]);

        $usuario->update([
            'id_rol' => self::ROLES_INVERSO[$data['rol']],
            'id_sucursal' => $data['id_sucursal'] ?? null,
        ]);

        return response()->json($usuario);
    }

    public function destroy(User $usuario)
    {
        try {
            \DB::transaction(function () use ($usuario) {
                \App\Models\Sucursal::where('id_gerente', $usuario->id_usuario)
                    ->update(['id_gerente' => null]);

                $usuario->delete();
            });
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'message' => 'No se puede eliminar: el usuario tiene ventas o cortes de caja registrados.',
            ], 422);
        }

        return response()->noContent();
    }
}