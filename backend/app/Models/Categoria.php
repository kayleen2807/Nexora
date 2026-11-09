<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Categoría de UNA sucursal (migración 2026_10_28): cada gerente escribe las suyas.
 * El nombre no se repite dentro de la sucursal sin importar mayúsculas ni espacios
 * (índice único sobre `lower(btrim(nombre))`). Se asigna al producto en `inventario.id_categoria`.
 *
 * @property int $id_categoria
 * @property string $nombre
 * @property bool|null $estado
 * @property int|null $id_sucursal
 */
class Categoria extends Model
{
    protected $table = 'categoria';

    protected $primaryKey = 'id_categoria';

    public $timestamps = false;

    protected $fillable = ['nombre', 'estado', 'id_sucursal'];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    /**
     * id de la categoría con ese nombre en la sucursal; si no existe, la crea.
     * Texto vacío = sin categoría (NULL). "frutas" reutiliza "Frutas" si ya existía.
     */
    public static function idPara(?string $nombre, int $idSucursal): ?int
    {
        $nombre = trim(preg_replace('/\s+/u', ' ', (string) $nombre));
        if ($nombre === '') {
            return null;
        }

        $buscar = fn () => static::where('id_sucursal', $idSucursal)
            ->whereRaw('lower(btrim(nombre)) = lower(?)', [$nombre])
            ->value('id_categoria');

        // insertOrIgnore = "ON CONFLICT DO NOTHING": si otro gerente de la misma sucursal
        // la crea al mismo tiempo, el índice único lo detiene sin lanzar error y la buscamos.
        if (! $buscar()) {
            DB::table('categoria')->insertOrIgnore(['nombre' => $nombre, 'estado' => true, 'id_sucursal' => $idSucursal]);
        }

        return (int) $buscar();
    }
}
