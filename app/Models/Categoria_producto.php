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
}
