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

    // Primary key configuration
    protected $primaryKey = 'id_unidad';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = true;

    // Relations
    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_unidad', 'id_unidad');
    }
}
