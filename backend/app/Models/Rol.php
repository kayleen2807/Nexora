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
}
