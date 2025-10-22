<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aprobacion extends Model
{
    protected $table = 'aprobacion';
    protected $primaryKey = 'id_aprobacion';
    public $timestamps = false;

    protected $fillable = [
        'id_solicitud',
        'decision',
        'observaciones',
        'id_usuario_autoridad',
        'monto_aprobado',
        'condiciones_aprobacion'
    ];

    protected $casts = [
        'fecha_aprobacion' => 'datetime',
        'monto_aprobado' => 'decimal:2'
    ];

    const DECISIONES = [
        'APROBADA' => 'Aprobada',
        'RECHAZADA' => 'Rechazada',
        'REQUIERE_REVISION' => 'Requiere_Revision'
    ];

    // Relaciones
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function usuarioAutoridad()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_autoridad');
    }
}