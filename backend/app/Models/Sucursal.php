<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sucursal extends Model {
    protected $table = 'sucursal';
    protected $primaryKey = 'id_sucursal';
    public $timestamps = false;

    protected $fillable = ['nombre', 'direccion', 'contacto', 'estado', 'id_gerente'];

    public function gerente(): BelongsTo{
        return $this->belongsTo(User::class, 'id_gerente', 'id_usuario');
    }
}