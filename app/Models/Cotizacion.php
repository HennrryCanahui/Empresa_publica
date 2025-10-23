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
        'fecha_validez',
        'tiempo_entrega_dias',
        'condiciones_pago',
        'id_usuario_compras',
        'estado',
        'observaciones'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function detalles()
    {
        return $this->hasMany(Detalle_Cotizacion::class, 'id_cotizacion', 'id_cotizacion');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'id_cotizacion', 'id_cotizacion');
    }

    public function usuario()
    {
        return $this->hasMany(User::class, 'id_usuario', 'id_usuario_compras');
    }
}
