<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
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
 * @property string|null $autorizacion pendiente | aprobado | rechazado; NULL = anterior al 2026-11-18 (no pasa por el admin)
 * @property int|null $id_sucursal_origen sucursal donde lo dio de alta el gerente
 * @property int|null $id_usuario_solicita gerente que lo dio de alta
 * @property Carbon|null $fecha_solicitud
 * @property Carbon|null $fecha_autorizacion
 * @property string|null $motivo_rechazo
 * @property-read Sucursal|null $sucursalOrigen
 * @property-read User|null $solicita
 */
class Producto extends Model
{
    /*
     * Autorización (decisión de Kay 2026-11-18): el producto que da de alta un gerente queda PENDIENTE y
     * se vende solo en su sucursal; el admin lo AUTORIZA (se agrega a todas las sucursales del negocio,
     * activo y con stock 0) o lo RECHAZA (se queda solo en la sucursal de origen).
     */
    public const PENDIENTE = 'pendiente';

    public const APROBADO = 'aprobado';

    public const RECHAZADO = 'rechazado';

    protected $table = 'producto';

    protected $primaryKey = 'id_producto';

    public $timestamps = false;

    protected $fillable = [
        'nombre', 'foto', 'precio', 'iva', 'estado', 'id_negocio',
        'autorizacion', 'id_sucursal_origen', 'id_usuario_solicita', 'fecha_solicitud',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
            'fecha_solicitud' => 'datetime',
            'fecha_autorizacion' => 'datetime',
        ];
    }

    public function sucursalOrigen(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal_origen', 'id_sucursal');
    }

    public function solicita(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_solicita', 'id_usuario');
    }

    /**
     * Productos que una sucursal puede ver y usar: los de antes del cambio (NULL), los autorizados y los
     * que dio de alta ELLA MISMA (aunque estén pendientes o rechazados). Los pendientes o rechazados de
     * OTRA sucursal no existen para ella (si no, un gerente podría "adoptarlos" sin pasar por el admin).
     */
    public function scopeVisibleEnSucursal(Builder $query, int $idSucursal): void
    {
        $query->where(fn (Builder $q) => $q->whereNull('producto.autorizacion')
            ->orWhere('producto.autorizacion', self::APROBADO)
            ->orWhere('producto.id_sucursal_origen', $idSucursal));
    }

    public function visibleEnSucursal(int $idSucursal): bool
    {
        return $this->autorizacion === null
            || $this->autorizacion === self::APROBADO
            || (int) $this->id_sucursal_origen === $idSucursal;
    }

    /**
     * Autoriza el producto: queda en TODAS las sucursales del negocio, activo y con stock 0. El precio e
     * IVA del catálogo pasan a ser los que tiene hoy en la sucursal de origen (el gerente pudo cambiarlos
     * mientras esperaba) y en cada sucursal recibe la misma categoría (por nombre). Se puede autorizar uno
     * que se había rechazado. Bloquea la fila del producto: dos clics seguidos no lo autorizan dos veces.
     */
    public function autorizar(): void
    {
        DB::transaction(function () {
            /** @var self $producto */
            $producto = static::whereKey($this->id_producto)->lockForUpdate()->firstOrFail();
            abort_unless(in_array($producto->autorizacion, [self::PENDIENTE, self::RECHAZADO], true), 422,
                'Este producto ya no está esperando autorización.');

            $origen = Inventario::with('categoria')->where('id_producto', $producto->id_producto)
                ->where('id_sucursal', $producto->id_sucursal_origen)->first();

            $producto->forceFill([
                'autorizacion' => self::APROBADO,
                'fecha_autorizacion' => now(),
                'motivo_rechazo' => null,
                'precio' => $origen?->precio ?? $producto->precio,
                'iva' => $origen?->iva ?? $producto->iva,
            ])->save();

            $idsSucursal = Sucursal::where('id_negocio', $producto->id_negocio)->pluck('id_sucursal')->all();
            $producto->agregarASucursales($idsSucursal, $origen?->categoria?->nombre);

            $this->setRawAttributes($producto->getAttributes(), true);
        });
    }

    /** Rechaza el producto: se queda solo en la sucursal de origen, con el motivo para el gerente. */
    public function rechazar(?string $motivo): void
    {
        DB::transaction(function () use ($motivo) {
            /** @var self $producto */
            $producto = static::whereKey($this->id_producto)->lockForUpdate()->firstOrFail();
            abort_unless($producto->autorizacion === self::PENDIENTE, 422, 'Este producto ya no está esperando autorización.');

            $producto->forceFill([
                'autorizacion' => self::RECHAZADO,
                'fecha_autorizacion' => now(),
                'motivo_rechazo' => $motivo,
            ])->save();

            $this->setRawAttributes($producto->getAttributes(), true);
        });
    }

    /**
     * Crea la fila de inventario (activo, stock 0, precio/IVA del catálogo) en las sucursales que aún no
     * tienen el producto. Las que ya lo tienen no se tocan (conservan su stock, precio y categoría).
     */
    public function agregarASucursales(array $idsSucursal, ?string $categoria = null): void
    {
        $yaLoTienen = Inventario::where('id_producto', $this->id_producto)->pluck('id_sucursal')->all();

        foreach (array_diff($idsSucursal, $yaLoTienen) as $idSucursal) {
            Inventario::create([
                'id_producto' => $this->id_producto,
                'id_sucursal' => $idSucursal,
                'existencias' => 0,
                'stock_minimo' => 5,
                'precio' => $this->precio,
                'iva' => $this->iva,
                'estado' => true,
                'id_categoria' => Categoria::idPara($categoria, (int) $idSucursal),
            ]);
        }
    }

    /** Una sucursal NUEVA recibe todos los productos autorizados del negocio (cada uno con su categoría). */
    public static function agregarAutorizadosA(Sucursal $sucursal): void
    {
        static::where('id_negocio', $sucursal->id_negocio)->where('autorizacion', self::APROBADO)
            ->orderBy('id_producto')->get()
            ->each(function (self $producto) use ($sucursal) {
                $categoria = Inventario::with('categoria')->where('id_producto', $producto->id_producto)
                    ->where('id_sucursal', $producto->id_sucursal_origen)->first()?->categoria?->nombre;
                $producto->agregarASucursales([$sucursal->id_sucursal], $categoria);
            });
    }

    /**
     * "¿Dónde hay?" (como en farmacias): las OTRAS sucursales activas del negocio que tienen el producto
     * activo, con dirección, teléfono y piezas; primero las que tienen más.
     */
    public function disponibilidad(int $exceptoSucursal): array
    {
        return Inventario::query()
            ->join('sucursal', 'sucursal.id_sucursal', '=', 'inventario.id_sucursal')
            ->where('inventario.id_producto', $this->id_producto)
            ->where('inventario.estado', true)
            ->where('sucursal.id_negocio', $this->id_negocio)
            ->where('sucursal.id_sucursal', '!=', $exceptoSucursal)
            ->where(fn ($q) => $q->whereNull('sucursal.estado')->orWhereRaw("lower(sucursal.estado) = 'activa'"))
            ->orderByDesc('inventario.existencias')
            ->orderBy('sucursal.nombre')
            ->get(['sucursal.nombre', 'sucursal.direccion', 'sucursal.contacto', 'inventario.existencias'])
            ->map(fn ($fila) => [
                'sucursal' => $fila->nombre,
                'direccion' => $fila->direccion,
                'contacto' => $fila->contacto,
                'existencias' => (int) $fila->existencias,
            ])->all();
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
