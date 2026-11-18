<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Reporte de un gerente o cajero (migración 2026_11_08_000002). Va "en cadena" (migración 2026_11_17_000000):
 * el de un cajero lo atiende el gerente de su sucursal (destino 'gerente'), que puede pasarlo al administrador;
 * el de un gerente va directo al administrador (destino 'admin').
 *
 * @property int $id_reporte
 * @property Carbon|null $fecha
 * @property int $id_negocio
 * @property int|null $id_sucursal
 * @property int|null $id_usuario autor (NULL si se borró al usuario)
 * @property string $tipo uno de Reporte::TIPOS
 * @property string $asunto
 * @property string $descripcion
 * @property string $estado uno de Reporte::ESTADOS
 * @property string|null $respuesta
 * @property Carbon|null $fecha_respuesta
 * @property int|null $id_usuario_responde
 * @property string $destino Reporte::DESTINO_GERENTE o Reporte::DESTINO_ADMIN (quién lo atiende)
 * @property Carbon|null $fecha_escalado cuándo el gerente lo pasó al administrador (NULL = no se pasó)
 * @property-read User|null $autor
 * @property-read User|null $responde
 * @property-read Sucursal|null $sucursal
 */
class Reporte extends Model
{
    protected $table = 'reporte';

    protected $primaryKey = 'id_reporte';

    public $timestamps = false;

    public const TIPOS = ['falla', 'faltante', 'caja', 'sugerencia', 'otro'];

    public const ESTADOS = ['pendiente', 'en_revision', 'resuelto'];

    public const DESTINO_GERENTE = 'gerente';

    public const DESTINO_ADMIN = 'admin';

    protected $fillable = [
        'fecha', 'id_negocio', 'id_sucursal', 'id_usuario', 'tipo', 'asunto', 'descripcion',
        'estado', 'respuesta', 'fecha_respuesta', 'id_usuario_responde', 'destino', 'fecha_escalado',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'fecha_respuesta' => 'datetime',
            'fecha_escalado' => 'datetime',
        ];
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /** Quien escribió la respuesta (gerente o administrador). */
    public function responde(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_responde', 'id_usuario');
    }

    /** ¿La sucursal tiene hoy un gerente? (sin gerente, los reportes de sus cajeros van al administrador) */
    public static function sucursalTieneGerente(?int $idSucursal): bool
    {
        $idGerente = Rol::idDe('gerente');

        return $idSucursal !== null && $idGerente !== null
            && User::where('id_sucursal', $idSucursal)->where('id_rol', $idGerente)->exists();
    }

    /**
     * Bandeja del administrador: lo que va dirigido a él + los reportes de cajeros de una sucursal que
     * se quedó SIN gerente (si no, nadie los atendería).
     */
    public function scopeParaAdmin(Builder $query, int $idNegocio): Builder
    {
        $idGerente = Rol::idDe('gerente');

        return $query->where('id_negocio', $idNegocio)->where(function (Builder $q) use ($idGerente) {
            $q->where('destino', self::DESTINO_ADMIN)
                ->orWhereNotExists(function ($sub) use ($idGerente) {
                    $sub->selectRaw('1')->from('usuario as g')
                        ->whereColumn('g.id_sucursal', 'reporte.id_sucursal')
                        ->where('g.id_rol', $idGerente);
                });
        });
    }

    /** Guarda estado y respuesta ya validados (lo usan admin y gerente al atender un reporte). */
    public function atender(array $data, int $idUsuario): void
    {
        $this->estado = $data['estado'];

        $respuesta = trim((string) ($data['respuesta'] ?? ''));
        if ($respuesta !== '' && $respuesta !== $this->respuesta) {
            $this->respuesta = $respuesta;
            $this->fecha_respuesta = now();
            $this->id_usuario_responde = $idUsuario;
        }
        $this->save();
    }

    /** Reglas para atender un reporte (cambiar estado y responder). */
    public static function reglasAtender(): array
    {
        return [
            'estado' => ['required', 'in:'.implode(',', self::ESTADOS)],
            'respuesta' => ['nullable', 'string', 'max:500'],
        ];
    }

    public static function mensajesAtender(): array
    {
        return [
            'estado.required' => 'Elige el estado.',
            'estado.in' => 'El estado no es válido.',
            'respuesta.max' => 'La respuesta no puede tener más de 500 caracteres.',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id_sucursal');
    }

    /** Reglas para crear un reporte (las usan gerente y cajero). */
    public static function reglas(): array
    {
        return [
            'tipo' => ['required', 'in:'.implode(',', self::TIPOS)],
            'asunto' => ['required', 'string', 'max:120'],
            'descripcion' => ['required', 'string', 'max:1000'],
        ];
    }

    public static function mensajes(): array
    {
        return [
            'tipo.required' => 'Elige el tipo de reporte.',
            'tipo.in' => 'El tipo de reporte no es válido.',
            'asunto.required' => 'Escribe el asunto.',
            'asunto.max' => 'El asunto no puede tener más de 120 caracteres.',
            'descripcion.required' => 'Describe el problema o la sugerencia.',
            'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
        ];
    }

    /** Lo que ven los paneles (mismo formato en gerente, cajero y admin). */
    public function aArreglo(?int $idUsuarioActual = null): array
    {
        return [
            'id' => $this->id_reporte,
            'fecha' => $this->fecha?->toIso8601String(),
            'autor' => $this->autor ? trim($this->autor->nombre.' '.$this->autor->apellido) : null,
            'rol_autor' => $this->autor?->rol ? strtolower($this->autor->rol->nombre) : null,
            'mio' => $idUsuarioActual !== null && (int) $this->id_usuario === $idUsuarioActual,
            'id_sucursal' => $this->id_sucursal,
            'sucursal' => $this->sucursal?->nombre,
            'tipo' => $this->tipo,
            'asunto' => $this->asunto,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'respuesta' => $this->respuesta,
            'fecha_respuesta' => $this->fecha_respuesta?->toIso8601String(),
            'rol_responde' => $this->responde?->rol ? strtolower($this->responde->rol->nombre) : null,
            'destino' => $this->destino,
            'fecha_escalado' => $this->fecha_escalado?->toIso8601String(),
        ];
    }
}
