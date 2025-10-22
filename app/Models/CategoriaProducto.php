<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoriaProducto extends Model
{
    use HasFactory;
    protected $table = 'categoria_producto';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relaciones
    public function productos()
    {
        return $this->hasMany(CatalogoProducto::class, 'id_categoria');
    }
}