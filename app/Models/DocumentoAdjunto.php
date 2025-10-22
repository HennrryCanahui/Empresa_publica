<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoAdjunto extends Model
{
    protected $table = 'documento_adjunto';
    protected $primaryKey = 'id_documento';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_solicitud',
        'nombre_archivo',
        'tipo_documento',
        'ruta_archivo',
        'tamano_bytes',
        'mime_type',
        'id_usuario_carga'
    ];

    protected $casts = [
        'tamano_bytes' => 'integer',
        'created_at' => 'datetime'
    ];

    const TIPOS_DOCUMENTO = [
        'ESPECIFICACION' => 'Especificación',
        'COTIZACION' => 'Cotización',
        'FACTURA' => 'Factura',
        'ORDEN_COMPRA' => 'Orden_Compra',
        'ACTA_ENTREGA' => 'Acta_Entrega',
        'OTRO' => 'Otro'
    ];

    // Relaciones
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function usuarioCarga()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_carga');
    }
}