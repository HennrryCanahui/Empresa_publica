<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoProducto extends Model
{
    protected $table = 'catalogo_producto';
    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tipo',
        'id_categoria',
        'unidad_medida',
        'precio_referencia',
        'especificaciones_tecnicas',
        'activo'
    ];

    protected $casts = [
        'precio_referencia' => 'decimal:2',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Tipos de productos
    const TIPOS = [
        'MATERIAL' => 'Material',
        'EQUIPO' => 'Equipo',
        'HERRAMIENTA' => 'Herramienta',
        'SERVICIO' => 'Servicio',
        'OTRO' => 'Otro'
    ];

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'id_categoria');
    }

    public function detallesSolicitud()
    {
        return $this->hasMany(DetalleSolicitud::class, 'id_producto');
    }

    public function detallesCotizacion()
    {
        return $this->hasMany(DetalleCotizacion::class, 'id_producto');
    }
}