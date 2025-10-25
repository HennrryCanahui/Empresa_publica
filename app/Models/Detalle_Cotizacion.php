<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detalle_Cotizacion extends Model
{
    protected $table = 'DETALLE_COTIZACION';

    protected $fillable = [
        'id_detalle_cotizacion',
        'id_cotizacion',
        'id_producto',
        'cantidad',
        'unidad_medida',
        'descripcion_proveedor',
        'precio_unitario',
        'precio_total',
        'observaciones'
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion', 'id_cotizacion');
    }

    public function producto()
    {
        return $this->belongsTo(Catalogo_producto::class, 'id_producto', 'id_producto');
    }

    protected $primaryKey = 'id_detalle_cotizacion';
    protected $keyType = 'int';
    public $incrementing = true; // Cambiar a true para Oracle auto-increment
    public $timestamps = false;

}
