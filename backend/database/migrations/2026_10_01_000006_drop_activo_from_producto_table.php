<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * La columna `activo` (migración 000004) duplicaba a `estado`, que ya existía
 * en `producto` desde el dump original con el mismo propósito. `estado` es el
 * flag real de activo/inactivo; aquí se elimina `activo`.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Productos de antes sin valor en `estado` se consideran activos
        DB::table('producto')->whereNull('estado')->update(['estado' => true]);

        if (Schema::hasColumn('producto', 'activo')) {
            // Si alguien ya desactivó productos usando `activo`, se conserva en `estado`
            DB::table('producto')->where('activo', false)->update(['estado' => false]);

            Schema::table('producto', function (Blueprint $table) {
                $table->dropColumn('activo');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('producto', 'activo')) {
            Schema::table('producto', function (Blueprint $table) {
                $table->boolean('activo')->default(true);
            });

            DB::table('producto')->update(['activo' => DB::raw('estado')]);
        }
    }
};
