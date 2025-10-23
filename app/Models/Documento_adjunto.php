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
        'tipo_documento',
        'ruta_archivo',
        'tamano_bytes',
        'mime_type',
        'id_usuario_carga'
    ];

    protected $primaryKey = 'id_documento';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = true;

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function usuarioCarga()
    {
        return $this->belongsTo(User::class, 'id_usuario_carga');
    }
}
