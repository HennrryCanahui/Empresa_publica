<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'AUDITORIA';

    protected $fillable = [
        'id_auditoria',
        'tabla_afectada',
        'id_registro',
        'accion',
        'datos_anteriores',
        'datos_nuevos',
        'id_usuario',
        'fecha_accion',
        'ip_address'
    ];

    protected $primaryKey = 'id_auditoria';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'fecha_accion' => 'datetime',
    ];
}
