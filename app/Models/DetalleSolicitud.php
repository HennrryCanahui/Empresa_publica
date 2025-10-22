<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleSolicitud extends Model
{
    protected $table = 'detalle_solicitud';
    protected $primaryKey = 'id_detalle';

    protected $fillable = [
        'id_solicitud',
        'id_producto',
        'cantidad',
        'especificaciones_adicionales',
        'precio_estimado_unitario',
        'precio_estimado_total'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_estimado_unitario' => 'decimal:2',
        'precio_estimado_total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relaciones
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
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
            if ($detalle->precio_estimado_unitario && $detalle->cantidad) {
                $detalle->precio_estimado_total = $detalle->precio_estimado_unitario * $detalle->cantidad;
            }
        });
    }
}