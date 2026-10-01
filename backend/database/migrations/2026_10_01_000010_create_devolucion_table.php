<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cada fila = piezas de UNA línea de venta (detalle_venta) que regresaron al
 * inventario, con el dinero reembolsado. La cancelación total también genera
 * filas aquí (por lo que quedaba sin devolver), así cortes y reportes solo
 * tienen que restar `devolucion.monto`.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('devolucion')) {
            Schema::create('devolucion', function (Blueprint $table) {
                $table->id('id_devolucion');
                $table->timestamp('fecha')->useCurrent();
                $table->foreignId('id_venta')->constrained('venta', 'id_venta');
                $table->foreignId('id_detalle')->constrained('detalle_venta', 'id_detalle');
                $table->foreignId('id_producto')->constrained('producto', 'id_producto');
                $table->integer('cantidad');
                $table->decimal('monto', 10, 2);
                $table->string('tipo', 20); // devolucion | cancelacion
                $table->string('motivo', 255);
                $table->foreignId('id_usuario')->constrained('usuario', 'id_usuario');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('devolucion');
    }
};
