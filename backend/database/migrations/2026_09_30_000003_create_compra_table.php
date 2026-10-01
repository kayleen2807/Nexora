<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('compra')) {
            Schema::create('compra', function (Blueprint $table) {
                $table->id('id_compra');
                $table->timestamp('fecha')->useCurrent();
                $table->foreignId('id_producto')->constrained('producto', 'id_producto');
                $table->foreignId('id_sucursal')->constrained('sucursal', 'id_sucursal');
                $table->foreignId('id_usuario')->constrained('usuario', 'id_usuario');
                $table->integer('cantidad');
                $table->decimal('costo_unitario', 10, 2);
                $table->decimal('total', 10, 2);
                $table->string('proveedor', 150)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('compra');
    }
};