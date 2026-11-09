<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Nexora atiende a varios negocios independientes. Cada negocio tiene UN dueño
 * (el administrador que se registró) y sus propias sucursales, productos y empleados.
 * Un negocio nunca ve los datos de otro.
 *
 * `id_propietario` no es único a propósito: en el futuro un mismo dueño podrá
 * registrar otro negocio distinto (ej. zapatería y papelería).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('negocio')) {
            Schema::create('negocio', function (Blueprint $table) {
                $table->id('id_negocio');
                $table->string('nombre', 150);
                $table->foreignId('id_propietario')->nullable()->constrained('usuario', 'id_usuario');
                $table->string('estado', 20)->default('activo');
                $table->timestamp('fecha_creacion')->useCurrent();
            });
        }

        foreach (['sucursal', 'producto', 'usuario'] as $tabla) {
            if (! Schema::hasColumn($tabla, 'id_negocio')) {
                Schema::table($tabla, function (Blueprint $table) {
                    $table->foreignId('id_negocio')->nullable()->constrained('negocio', 'id_negocio');
                });
            }
        }

        $this->asignarDatosExistentes();
    }

    /**
     * Los datos que ya existían (de antes de tener negocios) pasan al negocio del
     * PRIMER administrador. Si hay más admins, cada uno recibe su propio negocio vacío.
     */
    private function asignarDatosExistentes(): void
    {
        $idRolAdmin = DB::table('rol')->whereRaw('LOWER(nombre) = ?', ['administrador'])->value('id_rol');

        $admins = $idRolAdmin
            ? DB::table('usuario')->where('id_rol', $idRolAdmin)->whereNull('id_negocio')->orderBy('id_usuario')->get()
            : collect();

        $hayDatos = DB::table('sucursal')->whereNull('id_negocio')->exists()
            || DB::table('producto')->whereNull('id_negocio')->exists()
            || DB::table('usuario')->whereNull('id_negocio')->where('id_rol', '!=', $idRolAdmin ?? 0)->exists();

        if ($admins->isEmpty() && ! $hayDatos) {
            return; // BD nueva: nada que repartir
        }

        $primero = $admins->first();
        $idPrincipal = DB::table('negocio')->insertGetId([
            'nombre' => 'Mi negocio',
            'id_propietario' => $primero?->id_usuario,
            'estado' => 'activo',
        ], 'id_negocio');

        DB::table('sucursal')->whereNull('id_negocio')->update(['id_negocio' => $idPrincipal]);
        DB::table('producto')->whereNull('id_negocio')->update(['id_negocio' => $idPrincipal]);
        DB::table('usuario')->whereNull('id_negocio')->where('id_rol', '!=', $idRolAdmin ?? 0)->update(['id_negocio' => $idPrincipal]);

        if ($primero) {
            DB::table('usuario')->where('id_usuario', $primero->id_usuario)->update(['id_negocio' => $idPrincipal]);
        }

        foreach ($admins->skip(1) as $admin) {
            $id = DB::table('negocio')->insertGetId([
                'nombre' => trim('Negocio de '.$admin->nombre),
                'id_propietario' => $admin->id_usuario,
                'estado' => 'activo',
            ], 'id_negocio');
            DB::table('usuario')->where('id_usuario', $admin->id_usuario)->update(['id_negocio' => $id]);
        }
    }

    public function down(): void
    {
        foreach (['usuario', 'producto', 'sucursal'] as $tabla) {
            if (Schema::hasColumn($tabla, 'id_negocio')) {
                Schema::table($tabla, function (Blueprint $table) {
                    $table->dropForeign(['id_negocio']);
                    $table->dropColumn('id_negocio');
                });
            }
        }

        Schema::dropIfExists('negocio');
    }
};
