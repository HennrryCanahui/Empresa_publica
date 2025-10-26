<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rechazo_solicitud extends Model
{
    protected $table = 'RECHAZO_SOLICITUD';

    protected $fillable = [
        'id_rechazo',
        'id_solicitud',
        'motivo',
        'id_usuario',
        'fecha'
    ];

    protected $primaryKey = 'id_rechazo';
    public $incrementing = true;
    public $timestamps = false;
}
