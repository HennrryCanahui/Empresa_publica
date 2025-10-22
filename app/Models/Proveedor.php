<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedor';
    protected $primaryKey = 'id_proveedor';

    protected $fillable = [
        'codigo',
        'razon_social',
        'nit',
        'direccion',
        'telefono',
        'correo',
        'contacto_principal',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relaciones
    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'id_proveedor');
    }

    public function adquisiciones()
    {
        return $this->hasMany(Adquisicion::class, 'id_proveedor');
    }
}