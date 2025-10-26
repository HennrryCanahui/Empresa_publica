<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'NOTIFICACION';

    protected $fillable = [
        'id_notificacion',
        'id_usuario',
        'titulo',
        'mensaje',
        'leida',
        'fecha'
    ];

    protected $primaryKey = 'id_notificacion';
    public $incrementing = true;
    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
