<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    public const ADMINISTRACION = 'administracion';
    public const FARMACIA = 'farmacia';
    public const LABORATORIO = 'laboratorio';
    public const REPORTES = 'reportes';

    public const ROLES_BASE = [
        self::ADMINISTRACION,
        self::FARMACIA,
        self::LABORATORIO,
        self::REPORTES,
    ];

    protected $table = 'rol';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;

    protected $fillable = [
        'nombre_rol',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_rol', 'id_rol');
    }
}
