<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Reporte de un gerente o cajero para el administrador de su negocio (migración 2026_11_08_000002).
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
 * @property-read User|null $autor
 * @property-read Sucursal|null $sucursal
 */
class Reporte extends Model
{
    protected $table = 'reporte';

    protected $primaryKey = 'id_reporte';

    public $timestamps = false;

    public const TIPOS = ['falla', 'faltante', 'caja', 'sugerencia', 'otro'];

    public const ESTADOS = ['pendiente', 'en_revision', 'resuelto'];

    protected $fillable = [
        'fecha', 'id_negocio', 'id_sucursal', 'id_usuario', 'tipo', 'asunto', 'descripcion',
        'estado', 'respuesta', 'fecha_respuesta', 'id_usuario_responde',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'fecha_respuesta' => 'datetime',
        ];
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
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
        ];
    }
}
