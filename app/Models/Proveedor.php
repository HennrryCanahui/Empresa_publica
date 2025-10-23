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

    protected $primaryKey = 'id_proveedor';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = true;

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'id_proveedor', 'id_proveedor');
    }
}
