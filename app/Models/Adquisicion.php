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
        'fecha_adquisicion',
        'estado_entrega',
        'fecha_entrega_programada',
        'fecha_entrega_real',
        'observaciones',
        'id_usuario_compras'
    ];

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
        return $this->belongsTo(User::class, 'id_usuario_compras');
    }

    public function cotizacionSeleccionada()
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion_seleccionada');
    }
}
