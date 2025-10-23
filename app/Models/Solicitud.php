<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'SOLICITUD';

    protected $fillable = [
        'id_solicitud',
        'numero_solicitud',
        'fecha_creacion',
        'descripcion',
        'justificacion',
        'estado',
        'id_unida_solicitante',
        'id_usuario_creador',
        'prioridad',
        'fecha_limitie',
        'monto_total_estimado'
    ];
}
