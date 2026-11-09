<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reportes que gerentes y cajeros le mandan al administrador de SU negocio
 * (falla del sistema, faltante de producto, problema con caja/dinero, sugerencia u otro).
 * El administrador cambia el estado y puede contestar con un texto corto.
 *
 * Si se borra el usuario que lo escribió (o la sucursal), el reporte se conserva
 * con esas columnas en NULL (nullOnDelete): así borrar un empleado no falla por sus reportes.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reporte')) {
            return;
        }

        Schema::create('reporte', function (Blueprint $table) {
            $table->id('id_reporte');
            $table->timestamp('fecha')->useCurrent();
            $table->foreignId('id_negocio')->constrained('negocio', 'id_negocio');
            // usuario.id_usuario y sucursal.id_sucursal son serial (int4): integer() y no foreignId() (bigint)
            $table->integer('id_sucursal')->nullable();
            $table->foreign('id_sucursal')->references('id_sucursal')->on('sucursal')->nullOnDelete();
            $table->integer('id_usuario')->nullable();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->nullOnDelete();
            $table->string('tipo', 20);                       // falla | faltante | caja | sugerencia | otro
            $table->string('asunto', 120);
            $table->text('descripcion');
            $table->string('estado', 20)->default('pendiente'); // pendiente | en_revision | resuelto
            $table->string('respuesta', 500)->nullable();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->integer('id_usuario_responde')->nullable();
            $table->foreign('id_usuario_responde')->references('id_usuario')->on('usuario')->nullOnDelete();

            // La bandeja del admin filtra por negocio y estado
            $table->index(['id_negocio', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reporte');
    }
};
