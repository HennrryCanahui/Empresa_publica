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
    public $incrementing = true; // Cambiar a true para Oracle auto-increment
    public $timestamps = false; // Tabla solo tiene created_at, no updated_at
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function usuarioCarga()
    {
        return $this->belongsTo(User::class, 'id_usuario_carga');
    }
}
