<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    protected $table = 'presupuesto';
    protected $primaryKey = 'id_presupuesto';
    public $timestamps = false;

    protected $fillable = [
        'id_solicitud',
        'monto_estimado',
        'partida_presupuestaria',
        'disponibilidad_actual',
        'validacion',
        'observaciones',
        'id_usuario_presupuesto'
    ];

    protected $casts = [
        'monto_estimado' => 'decimal:2',
        'disponibilidad_actual' => 'decimal:2',
        'fecha_revision' => 'datetime'
    ];

    const VALIDACIONES = [
        'VALIDO' => 'Válido',
        'REQUIERE_AJUSTE' => 'Requiere_Ajuste',
        'RECHAZADO' => 'Rechazado'
    ];

    // Relaciones
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function usuarioPresupuesto()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_presupuesto');
    }
}