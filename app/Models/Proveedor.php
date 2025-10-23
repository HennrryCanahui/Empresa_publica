<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'PROVEEDOR';

    protected $fillable = [
        'id_proveedor',
        'codigo_proveedor',
        'razon_social',
        'nit',
        'direccion',
        'telefono',
        'correo',
        'contacto_principal',
        'activo'
    ];
}
