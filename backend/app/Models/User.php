<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nombre', 'apellido', 'correo', 'contrasena', 'id_rol', 'id_sucursal'])]
#[Hidden(['contrasena', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /** Busca enla tabla usuarios de nuestra bd */
    protected $table = 'usuario';

    /** identifica la llave primaria enla tabla usuarios de nuestra bd */
    protected $primaryKey = 'id_usuario';

    /** las timestamp las */
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
        ];
    }
}