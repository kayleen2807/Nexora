<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_sucursal
 * @property string $nombre
 * @property string $direccion
 * @property string|null $contacto
 * @property string|null $estado
 * @property int|null $id_gerente
 * @property int|null $id_negocio
 * @property-read User|null $gerente
 */
class Sucursal extends Model {
    protected $table = 'sucursal';
    protected $primaryKey = 'id_sucursal';
    public $timestamps = false;

    protected $fillable = ['nombre', 'direccion', 'contacto', 'estado', 'id_gerente', 'id_negocio'];

    public function gerente(): BelongsTo{
        return $this->belongsTo(User::class, 'id_gerente', 'id_usuario');
    }

    /*
     * Regla (decisión de Kay 2026-11-17): un gerente encabeza UNA sola sucursal y una sucursal tiene
     * como máximo UN gerente. La verdad es usuario.id_sucursal (donde trabaja); sucursal.id_gerente
     * ("encargado" de la tabla Sucursales) se mantiene sincronizado con eso.
     */

    /** El gerente que trabaja hoy en esta sucursal (sin contar a $excepto), o null si está libre. */
    public static function gerenteEn(int $idSucursal, ?int $excepto = null): ?User
    {
        return User::where('id_sucursal', $idSucursal)
            ->where('id_rol', Rol::idDe('gerente'))
            ->when($excepto, fn ($q) => $q->where('id_usuario', '!=', $excepto))
            ->first();
    }

    /** Deja a $idGerente como encargado SOLO de $idSucursal (lo quita de cualquier otra). */
    public static function ponerEncargado(int $idSucursal, int $idGerente): void
    {
        static::where('id_gerente', $idGerente)->where('id_sucursal', '!=', $idSucursal)->update(['id_gerente' => null]);
        static::where('id_sucursal', $idSucursal)->update(['id_gerente' => $idGerente]);
    }

    /** Bloquea la fila de la sucursal hasta que termine la transacción (dos admins a la vez no asignan dos gerentes). */
    public static function bloquear(int $idSucursal): ?self
    {
        return static::whereKey($idSucursal)->lockForUpdate()->first();
    }
}