<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsuarioModuloPermiso extends Model
{
    public const MODULOS_VALIDOS = [
        Rol::ADMINISTRACION,
        Rol::FARMACIA,
        Rol::LABORATORIO,
        Rol::REPORTES,
    ];

    protected $table = 'usuario_modulo_permiso';
    protected $primaryKey = 'id_usuario_modulo_permiso';

    protected $fillable = [
        'id_usuario',
        'modulo',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
