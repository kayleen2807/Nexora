<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as BaseCollection;

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
     * @param  Collection<int, Venta>|null  $ventas  ventas ya cargadas (las pasa resumenes()); null = las consulta aquí
     * @return array<string, mixed>
     */
    public function resumen(?Collection $ventas = null): array
    {
        $hasta = $this->fecha_cierre ?? now();

        $ventas ??= Venta::with(['metodoPago', 'devoluciones' => fn ($q) => $q->where('fecha', '<=', $hasta)])
            ->where('id_caja', $this->id_caja)
            ->where('id_cajero', $this->id_usuario)
            ->where('fecha_hora', '>=', $this->fecha_apertura)
            ->where('fecha_hora', '<=', $hasta)
            ->get();

        [$enEfectivo, $otras] = $ventas->partition(fn (Venta $v) => $v->esEfectivo());

        // Solo reembolsos hechos antes del cierre de ESTE turno (en lote llegan cargados hasta el último cierre)
        $devuelto = fn (Venta $v) => round((float) $v->devoluciones
            ->filter(fn (Devolucion $d) => $d->fecha <= $hasta)
            ->sum(fn (Devolucion $d) => (float) $d->monto), 2);

        $suma = fn ($col, $campo) => round($col->sum(fn (Venta $v) => $campo($v)), 2);
        $ventasEfectivo = $suma($enEfectivo, fn ($v) => (float) $v->total);
        $ventasTarjeta = $suma($otras, fn ($v) => (float) $v->total);
        $devEfectivo = $suma($enEfectivo, $devuelto);
        $devTarjeta = $suma($otras, $devuelto);
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

    /**
     * resumen() de VARIOS cortes con un número fijo de consultas (antes eran 3 por corte):
     * trae de una vez las ventas de todas sus cajas/cajeros en el rango de fechas y las
     * reparte en PHP a cada turno con el mismo criterio de resumen().
     *
     * @param  Collection<int, CorteCaja>  $cortes
     * @return BaseCollection<int, array<string, mixed>>
     */
    public static function resumenes(Collection $cortes): BaseCollection
    {
        if ($cortes->isEmpty()) {
            return collect();
        }

        $cortes->loadMissing(['caja', 'usuario']);
        $desde = $cortes->min('fecha_apertura');
        $hasta = $cortes->map(fn (CorteCaja $c) => $c->fecha_cierre ?? now())->max();

        $ventas = Venta::with(['metodoPago', 'devoluciones' => fn ($q) => $q->where('fecha', '<=', $hasta)])
            ->whereIn('id_caja', $cortes->pluck('id_caja')->unique())
            ->whereIn('id_cajero', $cortes->pluck('id_usuario')->unique())
            ->whereBetween('fecha_hora', [$desde, $hasta])
            ->get()
            // Cada lectura de un atributo con cast 'datetime' crea un Carbon nuevo: se lee UNA vez por venta
            ->map(fn (Venta $v) => ['venta' => $v, 'fecha' => $v->fecha_hora])
            ->groupBy(fn (array $x) => $x['venta']->id_caja.'-'.$x['venta']->id_cajero);

        return $cortes->map(function (CorteCaja $c) use ($ventas) {
            $desdeTurno = $c->fecha_apertura;
            $hastaTurno = $c->fecha_cierre ?? now();
            $propias = ($ventas->get($c->id_caja.'-'.$c->id_usuario) ?? collect())
                ->filter(fn (array $x) => $x['fecha'] >= $desdeTurno && $x['fecha'] <= $hastaTurno)
                ->pluck('venta');

            return $c->resumen(new Collection($propias->all()));
        })->values();
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
