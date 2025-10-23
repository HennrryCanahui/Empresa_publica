<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogo_producto extends Model
{
    protected $table = 'CATALOGO_PRODUCTO';

    protected $fillable = [
        'id_producto',
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


    public function categoria()
    {
        return $this->belongsTo(Categoria_producto::class, 'id_categoria');
    }

    public function detallesCotizacion()
    {
        return $this->hasMany(Detalle_Cotizacion::class, 'id_producto', 'id_producto');
    }

    protected $primaryKey = 'id_producto';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = true;


}
