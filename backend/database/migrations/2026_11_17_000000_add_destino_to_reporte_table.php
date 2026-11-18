<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reportes "en cadena": cajero → gerente → (si hace falta) administrador.
 *
 * - destino: con quién está el reporte. 'gerente' = lo atiende el gerente de la sucursal (lo envió un cajero);
 *   'admin' = lo atiende el administrador (lo envió un gerente, o un gerente se lo pasó).
 *   Default 'admin': los reportes que ya existían siguen en la bandeja del administrador, como hasta ahora.
 * - fecha_escalado: cuándo el gerente lo pasó al administrador (NULL = no se pasó).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reporte', function (Blueprint $table) {
            if (! Schema::hasColumn('reporte', 'destino')) {
                $table->string('destino', 10)->default('admin');   // gerente | admin
            }
            if (! Schema::hasColumn('reporte', 'fecha_escalado')) {
                $table->timestamp('fecha_escalado')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reporte', function (Blueprint $table) {
            if (Schema::hasColumn('reporte', 'fecha_escalado')) {
                $table->dropColumn('fecha_escalado');
            }
            if (Schema::hasColumn('reporte', 'destino')) {
                $table->dropColumn('destino');
            }
        });
    }
};
