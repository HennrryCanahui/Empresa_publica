<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialEstado extends Model
{
    protected $table = 'historial_estados';
    protected $primaryKey = 'id_historial';
    public $timestamps = false;

    protected $fillable = [
        'id_solicitud',
        'estado_anterior',
        'estado_nuevo',
        'id_usuario',
        'observaciones',
        'ip_address'
    ];

    protected $casts = [
        'fecha_cambio' => 'datetime'
    ];

    // Relaciones
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}