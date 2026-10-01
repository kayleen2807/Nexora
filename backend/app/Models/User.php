<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id_usuario
 * @property string $nombre
 * @property string $apellido
 * @property \Illuminate\Support\Carbon|null $fecha_nacimiento
 * @property string $correo
 * @property string $contrasena
 * @property string|null $remember_token
 * @property int $id_rol
 * @property int|null $id_sucursal
 * @property string $idioma
 * @property-read Rol|null $rol
 * @property-read Sucursal|null $sucursal
 */
#[Fillable(['nombre', 'apellido', 'fecha_nacimiento', 'correo', 'contrasena', 'id_rol', 'id_sucursal', 'idioma'])]
#[Hidden(['contrasena', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /** Busca enla tabla usuarios de nuestra bd */
    protected $table = 'usuario';

    /** identifica la llave primaria enla tabla usuarios de nuestra bd */
    protected $primaryKey = 'id_usuario';

    /** las timestamp las ponemos en false*/
    public $timestamps = false;

    protected $authPasswordName = 'contrasena';

    /**
     * Relación: un usuario pertenece a un rol.
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    /**
     * Relación: un usuario pertenece a una sucursal.
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id_sucursal');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'contrasena' => 'hashed',
            'fecha_nacimiento' => 'date',
        ];
    }
}