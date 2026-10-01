<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id_venta
 * @property Carbon|null $fecha_hora
 * @property string $subtotal numeric(10,2) suma sin IVA, llega como string
 * @property string $total numeric(10,2) con IVA, llega como string
 * @property int $id_sucursal
 * @property int $id_cajero
 * @property int $id_metodo_pago
 * @property int $id_caja
 * @property string $estado 'completada' | 'cancelada'
 * @property Carbon|null $fecha_cancelacion
 * @property int|null $id_usuario_cancela
 * @property string|null $motivo_cancelacion
 * @property-read User|null $cajero
 * @property-read MetodoPago|null $metodoPago
 * @property-read Caja|null $caja
 * @property-read Collection<int, DetalleVenta> $detalles
 * @property-read Collection<int, Devolucion> $devoluciones
 */
class Venta extends Model
{
    protected $table = 'venta';

    protected $primaryKey = 'id_venta';

    public $timestamps = false;

    public const COMPLETADA = 'completada';

    public const CANCELADA = 'cancelada';

    protected $fillable = [
        'fecha_hora', 'subtotal', 'total', 'id_sucursal', 'id_cajero', 'id_metodo_pago', 'id_caja',
        'estado', 'fecha_cancelacion', 'id_usuario_cancela', 'motivo_cancelacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
            'fecha_cancelacion' => 'datetime',
        ];
    }

    public function cajero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_cajero', 'id_usuario');
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_pago', 'id_metodo_pago');
    }

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class, 'id_caja', 'id_caja');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }

    public function devoluciones(): HasMany
    {
        return $this->hasMany(Devolucion::class, 'id_venta', 'id_venta');
    }

    public function esEfectivo(): bool
    {
        return strtolower($this->metodoPago->nombre ?? '') === 'efectivo';
    }

    public function estaCancelada(): bool
    {
        return $this->estado === self::CANCELADA;
    }

    /** Dinero ya reembolsado (devoluciones + cancelación). Usa la relación si ya se cargó. */
    public function montoDevuelto(): float
    {
        return round((float) $this->devoluciones->sum(fn (Devolucion $d) => (float) $d->monto), 2);
    }

    /**
     * Total vendido NETO de un conjunto de ventas: suma de totales menos lo reembolsado.
     * Se usa en los reportes (Mi sucursal, Resumen del admin) para no contar lo devuelto.
     */
    public static function totalNeto(Builder $ventas): float
    {
        $bruto = (float) (clone $ventas)->sum('total');
        $devuelto = (float) Devolucion::whereIn('id_venta', (clone $ventas)->select('id_venta'))->sum('monto');

        return round($bruto - $devuelto, 2);
    }
}
