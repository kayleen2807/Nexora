<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Categorías por sucursal: cada gerente crea las suyas y no se mezclan con las de otras sucursales
 * (Sur y Norte pueden tener cada una su "Frutas", con productos de su propia sucursal).
 
 */
return new class extends Migration
{
    
    public function up(): void
    {
        // La columna "estado" se agregó a mano en DBeaver; así queda registrada en las migraciones.
        if (!Schema::hasColumn('categoria', 'estado')) {
            Schema::table('categoria', function (Blueprint $table) {
                $table->boolean('estado')->nullable()->default(true);
            });
        }

        // 1) Cada categoría pertenece a UNA sucursal: la de su gerente.
        //    sucursal.id_sucursal es serial4 (int4): por eso integer() y no foreignId() (que sería bigint).
        Schema::table('categoria', function (Blueprint $table) {
            $table->integer('id_sucursal')->nullable();
            $table->foreign('id_sucursal')->references('id_sucursal')->on('sucursal');
        });

        // 2) El nombre solo se puede repetir en OTRA sucursal, no dentro de la misma
        //    ("Frutas", "frutas" y " FRUTAS " cuentan como la misma).
        
        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS categoria_sucursal_nombre_unico ON categoria (id_sucursal, lower(btrim(nombre)))');

        // 3) Par (categoría, sucursal) único: lo necesita la llave compuesta del paso siguiente.
        Schema::table('categoria', function (Blueprint $table) {
            $table->unique(['id_categoria', 'id_sucursal'], 'categoria_id_sucursal_uk');
        });

        // 4) La categoría de cada producto en cada sucursal vive en inventario.
    
        Schema::table('inventario', function (Blueprint $table) {
            $table->integer('id_categoria')->nullable();
            $table->index('id_categoria');

            // Una fila de inventario solo puede usar una categoría de SU MISMA sucursal:
            // la base de datos impide que Norte use una categoría creada por Sur.
            // Si id_categoria es NULL (producto sin categoría) no se valida nada.
            $table->foreign(['id_categoria', 'id_sucursal'], 'inventario_categoria_sucursal_fk')
                  ->references(['id_categoria', 'id_sucursal'])
                  ->on('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            $table->dropForeign('inventario_categoria_sucursal_fk');
            $table->dropIndex(['id_categoria']);
            $table->dropColumn('id_categoria');
        });

        Schema::table('categoria', function (Blueprint $table) {
            $table->dropUnique('categoria_id_sucursal_uk');
        });

        DB::statement('DROP INDEX IF EXISTS categoria_sucursal_nombre_unico');

        Schema::table('categoria', function (Blueprint $table) {
            $table->dropForeign(['id_sucursal']);
            $table->dropColumn('id_sucursal');
        });
    }
};