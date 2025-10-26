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
    // Cambiar a true para que Laravel recupere el ID generado por el trigger de Oracle
    public $incrementing = true;
    // Tabla no tiene created_at/updated_at
    public $timestamps = false;

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
