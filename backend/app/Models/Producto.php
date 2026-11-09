<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo de UN negocio (compartido por todas sus sucursales). Su precio/IVA/estado son
 * solo el valor POR DEFECTO: cada sucursal define los suyos en `inventario`.
 *
 * @property int $id_producto
 * @property int|null $id_negocio
 * @property string $nombre
 * @property string $precio numeric(10,2) sin IVA (por defecto), llega como string
 * @property string $iva numeric(5,2) porcentaje (por defecto), llega como string
 * @property bool $estado activo por defecto para sucursales nuevas
 * @property string|null $foto ruta relativa en storage/app/public/productos
 * @property string|null $codigo_barras
 * @property int|null $id_categoria
 */
class Producto extends Model
{
    protected $table = 'producto';

    protected $primaryKey = 'id_producto';

    public $timestamps = false;

    protected $fillable = ['nombre', 'foto', 'precio', 'iva', 'estado', 'id_negocio'];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    /** Precio con IVA incluido, redondeado a centavos. Única fórmula de todo el sistema. */
    public static function calcularPrecioFinal(float $precio, float $iva): float
    {
        return round($precio * (1 + $iva / 100), 2);
    }
}
