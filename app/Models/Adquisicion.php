<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adquisicion extends Model
{
    protected $table = 'adquisicion';
    protected $primaryKey = 'id_adquisicion';

    protected $fillable = [
        'numero_orden_compra',
        'id_solicitud',
        'id_cotizacion_seleccionada',
        'id_proveedor',
        'numero_factura',
        'monto_final',
        'fecha_adquisicion',
        'estado_entrega',
        'fecha_entrega_programada',
        'fecha_entrega_real',
        'observaciones',
        'id_usuario_compras'
    ];

    protected $casts = [
        'fecha_adquisicion' => 'date',
        'fecha_entrega_programada' => 'date',
        'fecha_entrega_real' => 'date',
        'monto_final' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const ESTADOS_ENTREGA = [
        'PENDIENTE' => 'Pendiente',
        'PARCIAL' => 'Parcial',
        'COMPLETA' => 'Completa',
        'CANCELADA' => 'Cancelada'
    ];

    // Relaciones
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function cotizacionSeleccionada()
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion_seleccionada');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function usuarioCompras()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_compras');
    }
}