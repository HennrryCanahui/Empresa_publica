<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adquisicion extends Model
{
    protected $table = 'ADQUISICION';

    protected $fillable = [
        'id_adquisicion',
        'numero_orden_compra',
        'id_solicitud',
        'id_cotizacion_seleccionada',
        'id_proveedor',
        'numero_factura',
        'monto_total',
        'monto_final',
        'fecha_orden_compra',
        'fecha_estimada_entrega',
        'fecha_adquisicion',
        'estado_entrega',
        'fecha_entrega',
        'fecha_entrega_programada',
        'fecha_entrega_real',
        'lugar_entrega',
        'condiciones_pago',
        'notas_entrega',
        'observaciones',
        'id_usuario_compras'
    ];

    // Primary key and timestamps
    protected $primaryKey = 'id_adquisicion';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = true;

    protected $casts = [
        'fecha_adquisicion' => 'datetime',
        'fecha_entrega_programada' => 'datetime',
        'fecha_entrega_real' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'monto_final' => 'decimal:2',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud', 'id_solicitud');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    public function usuarioCompras()
    {
        return $this->belongsTo(User::class, 'id_usuario_compras', 'id_usuario');
    }

    public function cotizacionSeleccionada()
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion_seleccionada', 'id_cotizacion');
    }
}
