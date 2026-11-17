<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    /**
     * ¿Se puede borrar el producto sin perder historia? null = sí; si no, el motivo para el usuario.
     * Un producto con ventas, compras o devoluciones NO se borra (los reportes y cortes dependen de
     * esas filas): se da de baja. Con $soloSucursal (gerente) tampoco se borra si otra sucursal lo usa.
     */
    public function motivoParaNoEliminar(?int $soloSucursal = null): ?string
    {
        $id = $this->id_producto;

        if (DetalleVenta::where('id_producto', $id)->exists()) {
            return 'Este producto tiene ventas registradas; dalo de baja para ocultarlo sin perder el historial.';
        }
        if (Compra::where('id_producto', $id)->exists()) {
            return 'Este producto tiene compras registradas; dalo de baja para ocultarlo sin perder el historial.';
        }
        if (Devolucion::where('id_producto', $id)->exists()) {
            return 'Este producto tiene devoluciones registradas; dalo de baja para ocultarlo sin perder el historial.';
        }
        if ($soloSucursal !== null
            && Inventario::where('id_producto', $id)->where('id_sucursal', '!=', $soloSucursal)->exists()) {
            return 'Otras sucursales también usan este producto; dalo de baja en la tuya.';
        }

        return null;
    }

    /**
     * Borra el producto, su inventario en todas las sucursales y su foto. Bloquea sus filas de inventario
     * y VUELVE a revisar dentro de la transacción (una venta pudo registrarse en ese instante).
     * Devuelve null si se borró, o el motivo por el que no.
     */
    public function eliminarSiSePuede(?int $soloSucursal = null): ?string
    {
        $foto = $this->foto;

        $motivo = DB::transaction(function () use ($soloSucursal) {
            Inventario::where('id_producto', $this->id_producto)->lockForUpdate()->get();

            $motivo = $this->motivoParaNoEliminar($soloSucursal);
            if ($motivo === null) {
                Inventario::where('id_producto', $this->id_producto)->delete();
                $this->delete();
            }

            return $motivo;
        });

        // El archivo se borra hasta que la BD confirmó (si se hubiera revertido, la foto seguiría sirviendo)
        if ($motivo === null && $foto) {
            Storage::disk('public')->delete($foto);
        }

        return $motivo;
    }
}
