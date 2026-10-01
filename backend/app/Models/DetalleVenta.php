<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Una fila por producto vendido dentro de una venta.
 *
 * @property int $id_detalle
 * @property int $id_venta
 * @property int $id_producto
 * @property int $cantidad
 * @property string $precio_unitario numeric(10,2) con IVA al momento de la venta
 * @property string $subtotal numeric(10,2) precio_unitario * cantidad
 * @property-read Producto|null $producto
 */
class DetalleVenta extends Model
{
    protected $table = 'detalle_venta';

    protected $primaryKey = 'id_detalle';

    public $timestamps = false;

    protected $fillable = ['id_venta', 'id_producto', 'cantidad', 'precio_unitario', 'subtotal'];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
