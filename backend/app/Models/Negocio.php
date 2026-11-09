<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Un negocio independiente (ej. "Zapatería López"). Su dueño es el administrador
 * que se registró; tiene sus propias sucursales, productos y empleados.
 *
 * @property int $id_negocio
 * @property string $nombre
 * @property int|null $id_propietario
 * @property string $estado
 * @property Carbon|null $fecha_creacion
 * @property-read User|null $propietario
 */
class Negocio extends Model
{
    protected $table = 'negocio';

    protected $primaryKey = 'id_negocio';

    public $timestamps = false;

    protected $fillable = ['nombre', 'id_propietario', 'estado'];

    protected function casts(): array
    {
        return [
            'fecha_creacion' => 'datetime',
        ];
    }

    public function propietario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_propietario', 'id_usuario');
    }

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class, 'id_negocio', 'id_negocio');
    }
}
