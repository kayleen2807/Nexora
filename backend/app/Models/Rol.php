<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_rol
 * @property string $nombre
 */
class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'id_rol';

    protected $fillable = ['nombre'];

    public $timestamps = false;

    /** id del rol por su nombre ('administrador', 'gerente', 'cajero'), sin importar mayúsculas. */
    public static function idDe(string $nombre): ?int
    {
        $id = static::whereRaw('LOWER(nombre) = ?', [strtolower($nombre)])->value('id_rol');

        return $id !== null ? (int) $id : null;
    }
}
