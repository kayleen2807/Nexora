<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Entrada de mercancía a una sucursal; al registrarse incrementa `inventario.existencias`.
 *
 * @property int $id_compra
 * @property Carbon $fecha
 * @property int $id_producto
 * @property int $id_sucursal
 * @property int $id_usuario
 * @property int $cantidad
 * @property string $costo_unitario numeric(10,2), llega como string
 * @property string $total numeric(10,2), llega como string
 * @property string|null $proveedor
 * @property-read Producto|null $producto
 */
class Compra extends Model
{
    protected $table = 'compra';

    protected $primaryKey = 'id_compra';

    public $timestamps = false;

    protected $fillable = ['fecha', 'id_producto', 'id_sucursal', 'id_usuario', 'cantidad', 'costo_unitario', 'total', 'proveedor'];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
