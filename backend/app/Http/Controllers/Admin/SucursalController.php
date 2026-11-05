<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SucursalController extends Controller
{
    public function index(){
        return Sucursal::orderBy('id_sucursal')->get();
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

        return response()->json(Sucursal::create($data), 201);
    }

    public function update(Request $request, Sucursal $sucursal){
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'direccion' => ['required', 'string'],
            'contacto' => ['nullable', 'digits:10'],
            'estado' => ['nullable', 'string', 'max:20'],
            'id_gerente' => ['nullable', 'integer', 'exists:usuario,id_usuario'],
        ], [
            'contacto.digits' => __('Contact must contain exactly 10 digits.'),
        ]);

        DB::transaction(function () use ($sucursal, $data) {
            $sucursal->update($data);

            if (!empty($data['id_gerente'])) {
                User::where('id_usuario', $data['id_gerente'])
                    ->update(['id_sucursal' => $sucursal->id_sucursal]);
            }
        });

        return response()->json($sucursal);
    }

    public function destroy(Sucursal $sucursal){
        DB::transaction(function () use ($sucursal) {
            User::where('id_sucursal', $sucursal->id_sucursal)
                ->update(['id_sucursal' => null]);

            $sucursal->delete();
        });

        return response()->noContent();
    }
}
