<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * El panel de Cajero necesita al menos 'Efectivo' y 'Tarjeta' en `metodo_pago`
 * (el dump original trae la tabla vacía). Solo inserta los que no existan,
 * comparando sin importar mayúsculas, para no duplicar los que algún
 * compañero ya haya capturado a mano.
 */
return new class extends Migration
{
    private const METODOS = ['Efectivo', 'Tarjeta'];

    public function up(): void
    {
        foreach (self::METODOS as $nombre) {
            $existe = DB::table('metodo_pago')
                ->whereRaw('LOWER(nombre) = ?', [strtolower($nombre)])
                ->exists();

            if (! $existe) {
                DB::table('metodo_pago')->insert(['nombre' => $nombre]);
            }
        }
    }

    public function down(): void
    {
        // No se borran: puede haber ventas que ya los referencian (FK venta.id_metodo_pago)
    }
};
