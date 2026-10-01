<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Precio, IVA y activo/inactivo pasan a ser POR SUCURSAL: cada gerente los
 * cambia solo en su sucursal. Se guardan en `inventario` (producto × sucursal).
 *
 * `precio` e `iva` NULL = usar el valor del catálogo (`producto`). Así las filas
 * existentes siguen vendiéndose al mismo precio sin copiar nada.
 * `estado` arranca con el valor que tenía el producto en el catálogo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            if (! Schema::hasColumn('inventario', 'precio')) {
                $table->decimal('precio', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('inventario', 'iva')) {
                $table->decimal('iva', 5, 2)->nullable();
            }
            if (! Schema::hasColumn('inventario', 'estado')) {
                $table->boolean('estado')->default(true);
            }
        });

        // Productos que ya estaban desactivados en el catálogo quedan desactivados en cada sucursal
        DB::table('inventario')
            ->whereIn('id_producto', DB::table('producto')->where('estado', false)->select('id_producto'))
            ->update(['estado' => false]);
    }

    public function down(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            $table->dropColumn(['precio', 'iva', 'estado']);
        });
    }
};
