<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $table = 'COTIZACION';

    protected $fillable = [
        'id_cotizacion',
        'numero_cotizacion',
        'id_solicitud',
        'id_proveedor',
        'monto_total',
        'documento_cotizacion',
        'fecha_cotizacion',
        'vigencia_cotizacion',
        'fecha_validez',
        'tiempo_entrega',
        'tiempo_entrega_dias',
        'garantia',
        'condiciones_pago',
        'id_usuario_compras',
        'estado',
        'seleccionada',
        'observaciones'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    public function detalles()
    {
        return $this->hasMany(Detalle_Cotizacion::class, 'id_cotizacion', 'id_cotizacion');
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud', 'id_solicitud');
    }

    public function usuarioCompras()
    {
        return $this->belongsTo(User::class, 'id_usuario_compras', 'id_usuario');
    }

    protected $primaryKey = 'id_cotizacion';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = true;
}
