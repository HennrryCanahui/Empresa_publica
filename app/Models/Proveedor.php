<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'PROVEEDOR';

    protected $fillable = [
        'id_proveedor',
        'codigo',
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
    public $incrementing = true;
    public $timestamps = true;

    // Accessor para mapear NIT a nit_rfc
    public function getNitRfcAttribute()
    {
        return $this->attributes['nit'] ?? null;
    }

    // Mutator para mapear nit_rfc a NIT
    public function setNitRfcAttribute($value)
    {
        $this->attributes['nit'] = $value;
    }

    // Evitar que nit_rfc se incluya en las queries
    protected function getArrayableAttributes()
    {
        $attributes = parent::getArrayableAttributes();
        unset($attributes['nit_rfc']);
        return $attributes;
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'id_proveedor', 'id_proveedor');
    }
}
