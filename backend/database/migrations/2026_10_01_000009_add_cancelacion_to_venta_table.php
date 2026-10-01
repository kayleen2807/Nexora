<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Una venta puede ser cancelada por el gerente: queda el registro (no se borra,
 * por auditoría) con quién, cuándo y por qué.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            if (! Schema::hasColumn('venta', 'estado')) {
                $table->string('estado', 20)->default('completada'); // completada | cancelada
            }
            if (! Schema::hasColumn('venta', 'fecha_cancelacion')) {
                $table->timestamp('fecha_cancelacion')->nullable();
            }
            if (! Schema::hasColumn('venta', 'id_usuario_cancela')) {
                $table->foreignId('id_usuario_cancela')->nullable()->constrained('usuario', 'id_usuario');
            }
            if (! Schema::hasColumn('venta', 'motivo_cancelacion')) {
                $table->string('motivo_cancelacion', 255)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->dropForeign(['id_usuario_cancela']);
            $table->dropColumn(['estado', 'fecha_cancelacion', 'id_usuario_cancela', 'motivo_cancelacion']);
        });
    }
};
