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
        'id_unidad_solicitante',
        'id_usuario_creador',
        'prioridad',
        'fecha_limite',
        'monto_total_estimado'
    ];

    protected $primaryKey = 'id_solicitud';
    protected $keyType = 'int';
    // Cambiar a true para que Laravel recupere el ID generado por el trigger de Oracle
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_limite' => 'date',
        'monto_total_estimado' => 'decimal:2'
    ];

    // La tabla usa UPDATED_AT pero no CREATED_AT
    // Solo manejaremos UPDATED_AT manualmente cuando sea necesario
    const UPDATED_AT = 'updated_at';

    /**
     * Obtener la clave de ruta para el modelo.
     */
    public function getRouteKeyName()
    {
        return 'id_solicitud';
    }

    // Relaciones
    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad_solicitante', 'id_unidad');
    }

    public function unidadSolicitante()
    {
        return $this->unidad();
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'id_usuario_creador', 'id_usuario');
    }

    public function usuarioCreador()
    {
        return $this->solicitante();
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

    public function aprobacion()
    {
        return $this->hasOne(Aprobacion::class, 'id_solicitud', 'id_solicitud');
    }

    public function historialEstados()
    {
        return $this->hasMany(Historial_estados::class, 'id_solicitud', 'id_solicitud');
    }

    public function documentos()
    {
        return $this->hasMany(Documento_adjunto::class, 'id_solicitud', 'id_solicitud');
    }

    public function adquisicion()
    {
        return $this->hasOne(Adquisicion::class, 'id_solicitud', 'id_solicitud');
    }
}