<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Productos nuevos de un gerente → el administrador los autoriza (se vuelven de TODAS las sucursales) o no.
 *
 * - autorizacion: 'pendiente' | 'aprobado' | 'rechazado'. NULL = producto creado antes de este cambio:
 *   sigue funcionando igual que siempre y no pasa por el administrador (decisión de Kay 2026-11-18).
 * - id_sucursal_origen / id_usuario_solicita: dónde y quién lo dio de alta (ON DELETE SET NULL: borrar la
 *   sucursal o al gerente no borra el producto).
 * - fecha_solicitud / fecha_autorizacion: cuándo se pidió y cuándo decidió el administrador.
 * - motivo_rechazo: lo que el administrador le contestó al gerente al rechazarlo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            if (! Schema::hasColumn('producto', 'autorizacion')) {
                $table->string('autorizacion', 10)->nullable();   // pendiente | aprobado | rechazado
            }
            if (! Schema::hasColumn('producto', 'id_sucursal_origen')) {
                $table->foreignId('id_sucursal_origen')->nullable()
                    ->constrained('sucursal', 'id_sucursal')->nullOnDelete();
            }
            if (! Schema::hasColumn('producto', 'id_usuario_solicita')) {
                $table->foreignId('id_usuario_solicita')->nullable()
                    ->constrained('usuario', 'id_usuario')->nullOnDelete();
            }
            if (! Schema::hasColumn('producto', 'fecha_solicitud')) {
                $table->timestamp('fecha_solicitud')->nullable();
            }
            if (! Schema::hasColumn('producto', 'fecha_autorizacion')) {
                $table->timestamp('fecha_autorizacion')->nullable();
            }
            if (! Schema::hasColumn('producto', 'motivo_rechazo')) {
                $table->string('motivo_rechazo', 255)->nullable();
            }
        });

        if (! Schema::hasIndex('producto', ['id_negocio', 'autorizacion'])) {
            Schema::table('producto', function (Blueprint $table) {
                $table->index(['id_negocio', 'autorizacion']);   // bandeja "Productos por autorizar" del admin
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('producto', ['id_negocio', 'autorizacion'])) {
            Schema::table('producto', function (Blueprint $table) {
                $table->dropIndex(['id_negocio', 'autorizacion']);
            });
        }

        Schema::table('producto', function (Blueprint $table) {
            if (Schema::hasColumn('producto', 'id_usuario_solicita')) {
                $table->dropConstrainedForeignId('id_usuario_solicita');
            }
            if (Schema::hasColumn('producto', 'id_sucursal_origen')) {
                $table->dropConstrainedForeignId('id_sucursal_origen');
            }
            foreach (['motivo_rechazo', 'fecha_autorizacion', 'fecha_solicitud', 'autorizacion'] as $columna) {
                if (Schema::hasColumn('producto', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }
};
