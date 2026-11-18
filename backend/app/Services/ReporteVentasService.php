<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * "Reportes de ventas" (admin: todas sus sucursales o una; gerente: la suya). Todas las cifras son NETAS con la
 * misma regla que el resto de Nexora (Venta::totalNeto): total de las ventas − lo devuelto de ESAS ventas. Una
 * cancelación también cuenta como devolución (crea filas en `devolucion`), así que no hay que tratarla aparte.
 * Cada grupo se calcula con UNA consulta agrupada en PostgreSQL (no se recorren las ventas en PHP).
 */
class ReporteVentasService
{
    public const MAX_DIAS = 366;

    /**
     * Valida ?desde=YYYY-MM-DD&hasta=YYYY-MM-DD (por defecto: el mes en curso hasta hoy). Máximo un año por
     * consulta para que la gráfica diaria y la consulta se mantengan ligeras.
     *
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    public function rango(Request $request): array
    {
        $datos = $request->validate([
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:desde'],
        ], [
            'desde.date_format' => 'La fecha "desde" no es válida.',
            'hasta.date_format' => 'La fecha "hasta" no es válida.',
            'hasta.after_or_equal' => 'La fecha "hasta" debe ser igual o posterior a "desde".',
        ]);

        $hoy = CarbonImmutable::today();
        $desde = isset($datos['desde']) ? CarbonImmutable::parse($datos['desde']) : $hoy->startOfMonth();
        $hasta = isset($datos['hasta']) ? CarbonImmutable::parse($datos['hasta']) : $hoy;
        abort_if($desde->diffInDays($hasta) >= self::MAX_DIAS, 422, 'Elige un periodo de máximo un año.');

        return [$desde, $hasta];
    }

    /** @param  int[]  $sucursales */
    public function generar(array $sucursales, CarbonImmutable $desde, CarbonImmutable $hasta): array
    {
        $inicio = $desde->startOfDay();
        $fin = $hasta->endOfDay();

        // Ventas del periodo con lo devuelto de cada una (subconsulta reutilizada por todos los grupos)
        $base = fn () => DB::table('venta as v')
            ->leftJoinSub(
                DB::table('devolucion')->selectRaw('id_venta, sum(monto) as devuelto')->groupBy('id_venta'),
                'd', 'd.id_venta', '=', 'v.id_venta'
            )
            ->whereIn('v.id_sucursal', $sucursales)
            ->whereBetween('v.fecha_hora', [$inicio, $fin]);
        $neto = 'coalesce(sum(v.total), 0) - coalesce(sum(d.devuelto), 0)';
        $numero = "count(*) filter (where v.estado <> 'cancelada')";

        $totales = $base()->selectRaw("coalesce(sum(v.total), 0) as bruto, coalesce(sum(d.devuelto), 0) as devuelto, $neto as neto, $numero as ventas")->first();

        $porDia = $base()->selectRaw("to_char(v.fecha_hora, 'YYYY-MM-DD') as dia, $neto as neto, $numero as ventas")
            ->groupBy('dia')->pluck('neto', 'dia');
        $ventasPorDia = $base()->selectRaw("to_char(v.fecha_hora, 'YYYY-MM-DD') as dia, $numero as ventas")
            ->groupBy('dia')->pluck('ventas', 'dia');

        $porMetodo = $base()->join('metodo_pago as m', 'm.id_metodo_pago', '=', 'v.id_metodo_pago')
            ->selectRaw("m.nombre as metodo, $neto as neto, $numero as ventas")
            ->groupBy('m.nombre')->orderByDesc('neto')->get();

        $porCajero = $base()->join('usuario as u', 'u.id_usuario', '=', 'v.id_cajero')
            ->selectRaw("trim(u.nombre || ' ' || u.apellido) as cajero, $neto as neto, $numero as ventas")
            ->groupBy('u.id_usuario', 'u.nombre', 'u.apellido')->orderByDesc('neto')->get();

        $porHora = $base()->selectRaw("extract(hour from v.fecha_hora)::int as hora, $neto as neto, $numero as ventas")
            ->groupBy('hora')->get()->keyBy('hora');

        // Productos: lo vendido por renglón menos lo devuelto de ESE renglón (cantidad e importe con IVA)
        $productos = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.id_venta', '=', 'dv.id_venta')
            ->join('producto as p', 'p.id_producto', '=', 'dv.id_producto')
            ->leftJoinSub(
                DB::table('devolucion')->selectRaw('id_detalle, sum(cantidad) as cantidad, sum(monto) as monto')->groupBy('id_detalle'),
                'dd', 'dd.id_detalle', '=', 'dv.id_detalle'
            )
            ->whereIn('v.id_sucursal', $sucursales)
            ->whereBetween('v.fecha_hora', [$inicio, $fin])
            ->selectRaw('p.id_producto as id, p.nombre, sum(dv.cantidad) - coalesce(sum(dd.cantidad), 0) as cantidad, sum(dv.subtotal) - coalesce(sum(dd.monto), 0) as importe')
            ->groupBy('p.id_producto', 'p.nombre')
            ->get()
            ->filter(fn ($p) => (int) $p->cantidad > 0);

        $numeroVentas = (int) $totales->ventas;
        $netoTotal = round((float) $totales->neto, 2);

        return [
            'desde' => $inicio->toDateString(),
            'hasta' => $fin->toDateString(),
            'totales' => [
                'neto' => $netoTotal,
                'bruto' => round((float) $totales->bruto, 2),
                'devoluciones' => round((float) $totales->devuelto, 2),
                'ventas' => $numeroVentas,
                'ticket_promedio' => $numeroVentas ? round($netoTotal / $numeroVentas, 2) : 0,
            ],
            // Todos los días del rango, también los que no tuvieron ventas (la gráfica no "salta" días)
            'por_dia' => collect(range(0, $inicio->diffInDays($fin->startOfDay())))->map(function ($i) use ($inicio, $porDia, $ventasPorDia) {
                $dia = $inicio->addDays($i)->toDateString();

                return ['dia' => $dia, 'neto' => round((float) ($porDia[$dia] ?? 0), 2), 'ventas' => (int) ($ventasPorDia[$dia] ?? 0)];
            })->values(),
            'por_metodo' => $porMetodo->map(fn ($m) => ['metodo' => $m->metodo, 'neto' => round((float) $m->neto, 2), 'ventas' => (int) $m->ventas])->values(),
            'por_cajero' => $porCajero->map(fn ($c) => ['cajero' => $c->cajero, 'neto' => round((float) $c->neto, 2), 'ventas' => (int) $c->ventas])->values(),
            'por_hora' => collect(range(0, 23))->map(fn ($h) => [
                'hora' => $h, 'neto' => round((float) ($porHora[$h]->neto ?? 0), 2), 'ventas' => (int) ($porHora[$h]->ventas ?? 0),
            ])->values(),
            'top_importe' => $productos->sortByDesc(fn ($p) => (float) $p->importe)->take(10)->map(fn ($p) => $this->producto($p))->values(),
            'top_cantidad' => $productos->sortByDesc(fn ($p) => (int) $p->cantidad)->take(10)->map(fn ($p) => $this->producto($p))->values(),
        ];
    }

    private function producto(object $p): array
    {
        return ['id' => (int) $p->id, 'nombre' => $p->nombre, 'cantidad' => (int) $p->cantidad, 'importe' => round((float) $p->importe, 2)];
    }
}
