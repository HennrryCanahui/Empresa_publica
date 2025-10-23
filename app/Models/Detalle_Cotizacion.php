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
        'precio_unitario',
        'precio_total',
        'observaciones'
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }
    public function producto()
    {
        return $this->belongsTo(Catalogo_producto::class);
    }

    protected $primaryKey = 'id_detalle_cotizacion';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = false;

}
