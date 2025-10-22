<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCotizacion extends Model
{
    protected $table = 'detalle_cotizacion';
    protected $primaryKey = 'id_detalle_cotizacion';
    public $timestamps = false;

    protected $fillable = [
        'id_cotizacion',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'precio_total',
        'observaciones'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'precio_total' => 'decimal:2'
    ];

    // Relaciones
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion');
    }

    public function producto()
    {
        return $this->belongsTo(CatalogoProducto::class, 'id_producto');
    }

    // Calcular precio total
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detalle) {
            if ($detalle->precio_unitario && $detalle->cantidad) {
                $detalle->precio_total = $detalle->precio_unitario * $detalle->cantidad;
            }
        });
    }
}