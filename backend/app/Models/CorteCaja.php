<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Turno de un cajero en una caja: se abre con un fondo inicial y se cierra
 * con el efectivo contado físicamente.
 *
 * @property int $id_corte
 * @property Carbon $fecha_apertura
 * @property Carbon|null $fecha_cierre
 * @property string $monto_inicial numeric(10,2), llega como string
 * @property string|null $monto_final numeric(10,2) efectivo contado al cerrar
 * @property int $id_caja
 * @property int $id_usuario
 * @property-read Caja|null $caja
 * @property-read User|null $usuario
 */
class CorteCaja extends Model
{
    protected $table = 'corte_caja';

    protected $primaryKey = 'id_corte';

    public $timestamps = false;

    protected $fillable = ['fecha_apertura', 'fecha_cierre', 'monto_inicial', 'monto_final', 'id_caja', 'id_usuario'];

    protected function casts(): array
    {
        return [
            'fecha_apertura' => 'datetime',
            'fecha_cierre' => 'datetime',
        ];
    }

    /** Turno sin cerrar del usuario (un cajero solo puede tener uno a la vez). */
    public static function abiertoDe(int $idUsuario): ?self
    {
        return static::with('caja')
            ->where('id_usuario', $idUsuario)
            ->whereNull('fecha_cierre')
            ->latest('fecha_apertura')
            ->first();
    }

    /**
     * Cifras del turno. Ventas = las que hizo este cajero en esta caja entre la apertura
     * y el cierre (o ahora, si sigue abierto). Reembolsos = devoluciones/cancelaciones de
     * ESAS ventas hechas antes del cierre (después del corte ya no salen de este cajón).
     *
     * @return array<string, mixed>
     */
    public function resumen(): array
    {
        $hasta = $this->fecha_cierre ?? now();

        $ventas = Venta::with(['metodoPago', 'devoluciones' => fn ($q) => $q->where('fecha', '<=', $hasta)])
            ->where('id_caja', $this->id_caja)
            ->where('id_cajero', $this->id_usuario)
            ->where('fecha_hora', '>=', $this->fecha_apertura)
            ->where('fecha_hora', '<=', $hasta)
            ->get();

        [$enEfectivo, $otras] = $ventas->partition(fn (Venta $v) => $v->esEfectivo());

        $suma = fn ($col, $campo) => round($col->sum(fn (Venta $v) => $campo($v)), 2);
        $ventasEfectivo = $suma($enEfectivo, fn ($v) => (float) $v->total);
        $ventasTarjeta = $suma($otras, fn ($v) => (float) $v->total);
        $devEfectivo = $suma($enEfectivo, fn ($v) => $v->montoDevuelto());
        $devTarjeta = $suma($otras, fn ($v) => $v->montoDevuelto());
        $inicial = (float) $this->monto_inicial;
        // Lo que debería haber físicamente en el cajón
        $esperado = round($inicial + $ventasEfectivo - $devEfectivo, 2);
        $contado = $this->monto_final !== null ? round((float) $this->monto_final, 2) : null;

        return [
            'id' => $this->id_corte,
            'id_caja' => $this->id_caja,
            'caja' => $this->caja?->numero_caja,
            'id_sucursal' => $this->caja?->id_sucursal,
            'cajero' => $this->usuario ? trim($this->usuario->nombre.' '.$this->usuario->apellido) : null,
            'apertura' => $this->fecha_apertura->toIso8601String(),
            'cierre' => $this->fecha_cierre?->toIso8601String(),
            'monto_inicial' => $inicial,
            'num_ventas' => $ventas->count(),
            'ventas_efectivo' => $ventasEfectivo,
            'ventas_tarjeta' => $ventasTarjeta,
            'devoluciones_efectivo' => $devEfectivo,
            'devoluciones_tarjeta' => $devTarjeta,
            'total' => round($ventasEfectivo + $ventasTarjeta - $devEfectivo - $devTarjeta, 2),
            'efectivo_esperado' => $esperado,
            'efectivo_contado' => $contado,
            // > 0 sobrante, < 0 faltante, 0 cuadrada; null si el turno sigue abierto
            'diferencia' => $contado !== null ? round($contado - $esperado, 2) : null,
        ];
    }

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class, 'id_caja', 'id_caja');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
