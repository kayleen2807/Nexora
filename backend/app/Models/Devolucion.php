<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Piezas de una línea de venta que regresaron al inventario y su reembolso.
 * La cancelación total también se registra aquí (tipo 'cancelacion').
 *
 * @property int $id_devolucion
 * @property Carbon $fecha
 * @property int $id_venta
 * @property int $id_detalle
 * @property int $id_producto
 * @property int $cantidad
 * @property string $monto numeric(10,2) dinero reembolsado, llega como string
 * @property string $tipo 'devolucion' | 'cancelacion'
 * @property string $motivo
 * @property int $id_usuario gerente que la autorizó
 * @property-read Venta|null $venta
 * @property-read User|null $usuario
 */
class Devolucion extends Model
{
    protected $table = 'devolucion';

    protected $primaryKey = 'id_devolucion';

    public $timestamps = false;

    public const DEVOLUCION = 'devolucion';

    public const CANCELACION = 'cancelacion';

    protected $fillable = ['fecha', 'id_venta', 'id_detalle', 'id_producto', 'cantidad', 'monto', 'tipo', 'motivo', 'id_usuario'];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
        ];
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
