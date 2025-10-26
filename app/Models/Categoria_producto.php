<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria_producto extends Model
{
    protected $table = 'CATEGORIA_PRODUCTO';

    protected $fillable = [
        'id_categoria',
        'codigo',
        'nombre',
        'descripcion',
        'activo'
    ];

    protected $primaryKey = 'id_categoria';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    public function productos()
    {
        return $this->hasMany(Catalogo_producto::class, 'id_categoria', 'id_categoria');
    }
}
