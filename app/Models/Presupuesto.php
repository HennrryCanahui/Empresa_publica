<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    protected $table = 'PRESUPUESTO';

    protected $fillable = [
        'id_presupuesto',
        'id_solicitud',
        'monto_presupuestado',
        'monto_estimado',
        'partida_presupuestaria',
        'disponibilidad_actual',
        'validado',
        'validacion',
        'fecha_validacion',
        'fecha_revision',
        'observaciones',
        'id_usuario_presupuesto',
        'id_usuario_presupuestario'
    ];

    protected $primaryKey = 'id_presupuesto';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = true;

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud', 'id_solicitud');
    }

    public function usuarioPresupuesto()
    {
        return $this->belongsTo(User::class, 'id_usuario_presupuesto', 'id_usuario');
    }

    public function usuarioPresupuestario()
    {
        return $this->usuarioPresupuesto();
    }
}
