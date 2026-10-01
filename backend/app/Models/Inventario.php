<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Un producto dentro de UNA sucursal: su stock y, desde la migración 000008,
 * su precio, IVA y si está activo EN ESA SUCURSAL.
 * `precio`/`iva` NULL = se usa el valor del catálogo (`producto`).
 *
 * @property int $id_inventario
 * @property int $id_sucursal
 * @property int $id_producto
 * @property int $existencias
 * @property int $stock_minimo
 * @property string|null $fechas_actualizacion
 * @property string|null $precio numeric(10,2) sin IVA; NULL = precio del catálogo
 * @property string|null $iva numeric(5,2); NULL = IVA del catálogo
 * @property bool $estado activo/inactivo en esta sucursal
 * @property-read Producto|null $producto
 */
class Inventario extends Model
{
    protected $table = 'inventario';

    protected $primaryKey = 'id_inventario';

    public $timestamps = false;

    protected $fillable = ['id_producto', 'id_sucursal', 'existencias', 'stock_minimo', 'precio', 'iva', 'estado'];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    /** Precio sin IVA que aplica en esta sucursal. */
    public function precioBase(Producto $producto): float
    {
        return (float) ($this->precio ?? $producto->precio);
    }

    /** % de IVA que aplica en esta sucursal. */
    public function ivaEfectivo(Producto $producto): float
    {
        return (float) ($this->iva ?? $producto->iva);
    }

    /** Precio con IVA que paga el cliente en esta sucursal. */
    public function precioFinal(Producto $producto): float
    {
        return Producto::calcularPrecioFinal($this->precioBase($producto), $this->ivaEfectivo($producto));
    }
}
