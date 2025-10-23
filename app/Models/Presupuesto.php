<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    protected $table = 'PRESUPUESTO';

    protected $fillable = [
        'id_presupuesto',
        'id_solicitud',
        'monto_estimado',
        'partida_presupuestaria',
        'disponibilidad_actual',
        'validacion',
        'fecha_revision',
        'id_usuario_presupuestario'
    ];

    protected $primaryKey = 'id_presupuesto';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = true;

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function usuarioPresupuesto()
    {
        return $this->belongsTo(User::class, 'id_usuario_presupuestario');
    }
}
