<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $table = 'cotizacion';
    protected $primaryKey = 'id_cotizacion';

    protected $fillable = [
        'numero_cotizacion',
        'id_solicitud',
        'id_proveedor',
        'monto_total',
        'documento_cotizacion',
        'fecha_cotizacion',
        'fecha_validez',
        'tiempo_entrega_dias',
        'condiciones_pago',
        'id_usuario_compras',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_cotizacion' => 'date',
        'fecha_validez' => 'date',
        'monto_total' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const ESTADOS = [
        'ACTIVA' => 'Activa',
        'SELECCIONADA' => 'Seleccionada',
        'DESCARTADA' => 'Descartada'
    ];

    // Relaciones
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function usuarioCompras()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_compras');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCotizacion::class, 'id_cotizacion');
    }

    public function adquisicion()
    {
        return $this->hasOne(Adquisicion::class, 'id_cotizacion_seleccionada');
    }
}