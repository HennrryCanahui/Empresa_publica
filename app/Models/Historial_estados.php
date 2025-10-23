<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Historial_estados extends Model
{
    protected $table = 'HISTORIAL_ESTADOS';

    protected $fillable = [
        'id_historial',
        'id_solicitud',
        'estado_anterior',
        'estado_nuevo',
        'fecha_cambio',
        'id_usuario',
        'observaciones',
        'ip_address'
    ];

    protected $primaryKey = 'id_historial';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'fecha_cambio' => 'datetime',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
