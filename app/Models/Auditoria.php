<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';
    protected $primaryKey = 'id_auditoria';
    public $timestamps = false;

    protected $fillable = [
        'tabla_afectada',
        'id_registro',
        'accion',
        'datos_anteriores',
        'datos_nuevos',
        'id_usuario',
        'ip_address'
    ];

    protected $casts = [
        'fecha_accion' => 'datetime',
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array'
    ];

    const ACCIONES = [
        'INSERT' => 'INSERT',
        'UPDATE' => 'UPDATE',
        'DELETE' => 'DELETE'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}