<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    protected $table = 'PRESUPUESTO';

    protected $fillable = [
        'id_presupuesto',
        'id_solicitud',
        'monto_estimado',
        'partida_presupuestaria',
        'disponibilidad_actual',
        'validacion',
        'fecha_revision',
        'id_usuario_presupuestario'
    ];
}
