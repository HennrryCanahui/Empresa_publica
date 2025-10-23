<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aprobacion extends Model
{
    protected $table = 'APROBACION';

    protected $fillable = [
        'id_aprobacion',
        'id_solicitud',
        'decision',
        'observaciones',
        'fecha_aprobacion',
        'id_usuario_autoridad',
        'monto_aprobado',
        'condiciones_aprobacion'
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function usuarioAutoridad()
    {
        return $this->belongsTo(User::class, 'id_usuario_autoridad');
    }
}
