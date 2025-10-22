<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitud';
    protected $primaryKey = 'id_solicitud';

    protected $fillable = [
        'numero_solicitud',
        'descripcion',
        'justificacion',
        'estado',
        'id_unidad_solicitante',
        'id_usuario_creador',
        'prioridad',
        'fecha_limite',
        'monto_total_estimado'
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_limite' => 'date',
        'monto_total_estimado' => 'decimal:2',
        'updated_at' => 'datetime'
    ];

    // Estados posibles de la solicitud
    const ESTADOS = [
        'CREADA' => 'Creada',
        'EN_PRESUPUESTO' => 'En_Presupuesto',
        'PRESUPUESTADA' => 'Presupuestada',
        'EN_COTIZACION' => 'En_Cotizacion',
        'COTIZADA' => 'Cotizada',
        'EN_APROBACION' => 'En_Aprobacion',
        'APROBADA' => 'Aprobada',
        'RECHAZADA' => 'Rechazada',
        'EN_ADQUISICION' => 'En_Adquisicion',
        'COMPLETADA' => 'Completada',
        'CANCELADA' => 'Cancelada'
    ];

    // Relaciones
    public function unidadSolicitante()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad_solicitante');
    }

    public function usuarioCreador()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_creador');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleSolicitud::class, 'id_solicitud');
    }

    public function presupuesto()
    {
        return $this->hasOne(Presupuesto::class, 'id_solicitud');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'id_solicitud');
    }

    public function aprobacion()
    {
        return $this->hasOne(Aprobacion::class, 'id_solicitud');
    }

    public function adquisicion()
    {
        return $this->hasOne(Adquisicion::class, 'id_solicitud');
    }

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstado::class, 'id_solicitud');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoAdjunto::class, 'id_solicitud');
    }
    
    // Método para cambiar el estado
    public function cambiarEstado($nuevoEstado, $idUsuario, $observaciones = null)
    {
        $estadoAnterior = $this->estado;
        $this->estado = $nuevoEstado;
        $this->save();

        // Registrar en el historial
        return HistorialEstado::create([
            'id_solicitud' => $this->id_solicitud,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $nuevoEstado,
            'id_usuario' => $idUsuario,
            'observaciones' => $observaciones
        ]);
    }
}