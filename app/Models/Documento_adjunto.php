<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento_adjunto extends Model
{
    protected $table = 'DOCUMENTO_ADJUNTO';

    protected $fillable = [
        'id_documento',
        'id_solicitud',
        'nombre_archivo',
        'tipo_archivo',
        'ruta_archivo',
        'tamanio_bytes',
        'mime_type',
        'id_usuario_carga'
    ];
}
