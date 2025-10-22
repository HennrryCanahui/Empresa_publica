<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $table = 'unidad';
    protected $primaryKey = 'id_unidad';

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo',
        'descripcion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relaciones
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_unidad');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'id_unidad_solicitante');
    }
}