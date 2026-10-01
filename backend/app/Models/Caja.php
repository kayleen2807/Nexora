<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Caja registradora física de una sucursal.
 * `estado` ('Activa'/'Inactiva') indica si se puede usar; si está "abierta"
 * (en turno) se sabe por un `corte_caja` sin `fecha_cierre`.
 *
 * @property int $id_caja
 * @property int $numero_caja
 * @property string|null $estado
 * @property int $id_sucursal
 * @property-read CorteCaja|null $corteAbierto
 */
class Caja extends Model
{
    protected $table = 'caja';

    protected $primaryKey = 'id_caja';

    public $timestamps = false;

    public const ACTIVA = 'Activa';

    public const INACTIVA = 'Inactiva';

    protected $fillable = ['numero_caja', 'estado', 'id_sucursal'];

    /** Turno en curso de esta caja (a lo más uno). */
    public function corteAbierto(): HasOne
    {
        return $this->hasOne(CorteCaja::class, 'id_caja', 'id_caja')->whereNull('fecha_cierre');
    }
}
