<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'SOLICITUD';

    protected $fillable = [
        'id_solicitud',
        'numero_solicitud',
        'fecha_creacion',
        'descripcion',
        'justificacion',
        'estado',
        'id_unida_solicitante',
        'id_usuario_creador',
        'prioridad',
        'fecha_limitie',
        'monto_total_estimado'
    ];

    protected $primaryKey = 'id_solicitud';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = true;

    /**
     * Obtener la clave de ruta para el modelo.
     */
    public function getRouteKeyName()
    {
        return 'id_solicitud';
    }

    // ... resto de las relaciones
    public function unidadSolicitante()
    {
        return $this->belongsTo(Unidad::class, 'id_unida_solicitante');
    }

    public function usuarioCreador()
    {
        return $this->belongsTo(User::class, 'id_usuario_creador');
    }

    public function detalles()
    {
        return $this->hasMany(Detalle_solicitud::class, 'id_solicitud', 'id_solicitud');
    }

    public function presupuesto()
    {
        return $this->hasOne(Presupuesto::class, 'id_solicitud', 'id_solicitud');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'id_solicitud', 'id_solicitud');
    }

    public function aprobaciones()
    {
        return $this->hasMany(Aprobacion::class, 'id_solicitud', 'id_solicitud');
    }

    public function historialEstados()
    {
        return $this->hasMany(Historial_estados::class, 'id_solicitud', 'id_solicitud');
    }

    public function documentos()
    {
        return $this->hasMany(Documento_adjunto::class, 'id_solicitud', 'id_solicitud');
    }

    public function adquisiciones()
    {
        return $this->hasMany(Adquisicion::class, 'id_solicitud', 'id_solicitud');
    }
}