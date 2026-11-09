<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Un producto aparece UNA sola vez en el inventario de cada sucursal. El código ya lo
 * respeta; este índice hace que la BD también lo garantice (por si dos peticiones
 * llegan al mismo tiempo o alguien inserta a mano).
 */
return new class extends Migration
{
    public function up(): void
    {
        $duplicados = DB::table('inventario')
            ->select('id_producto', 'id_sucursal')
            ->groupBy('id_producto', 'id_sucursal')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        if ($duplicados > 0) {
            throw new RuntimeException(
                "Hay {$duplicados} producto(s) repetidos en el inventario de una misma sucursal. "
                .'Unifícalos antes de correr esta migración.'
            );
        }

        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS inventario_producto_sucursal_unico ON inventario (id_producto, id_sucursal)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS inventario_producto_sucursal_unico');
    }
};
