<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detalle_solicitud extends Model
{
    protected $table = 'DETALLE_SOLICITUD';

    protected $fillable = [
        'id_detalle',
        'id_solicitud',
        'id_producto',
        'id_unidad',
        'cantidad',
        'especificaciones_tecnicas',
        'especificaciones_adicionales',
        'precio_estimado',
        'precio_estimado_unitario',
        'subtotal_estimado',
        'precio_estimado_total'
    ];

    protected $primaryKey = 'id_detalle';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = true;

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud', 'id_solicitud');
    }

    public function producto()
    {
        return $this->belongsTo(Catalogo_producto::class, 'id_producto', 'id_producto');
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad', 'id_unidad');
    }
}
