<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $table = 'UNIDAD';

    protected $fillable = [
        'id_unidad',
        'nombre_unidad',
        'descripcion',
        'activo'
    ];
}
