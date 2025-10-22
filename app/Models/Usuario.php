<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    const ROLES = [
        'Administrador' => 'Administrador',
        'Solicitante' => 'Solicitante',
        'Presupuesto' => 'Presupuesto',
        'Compras' => 'Compras',
        'Autoridad' => 'Autoridad'
    ];

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'contrasena',
        'rol',
        'id_unidad',
        'telefono',
        'activo'
    ];

    protected $hidden = [
        'contrasena',
    ];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    protected $casts = [
        'activo' => 'boolean',
        'ultimo_acceso' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Mutador para encriptar la contraseña
    public function setContrasenaAttribute($value)
    {
        $this->attributes['contrasena'] = bcrypt($value);
    }

    // Relaciones
    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad');
    }

    public function solicitudesCreadas()
    {
        return $this->hasMany(Solicitud::class, 'id_usuario_creador');
    }

    public function presupuestos()
    {
        return $this->hasMany(Presupuesto::class, 'id_usuario_presupuesto');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'id_usuario_compras');
    }

    public function aprobaciones()
    {
        return $this->hasMany(Aprobacion::class, 'id_usuario_autoridad');
    }

    public function adquisiciones()
    {
        return $this->hasMany(Adquisicion::class, 'id_usuario_compras');
    }

    // Método para verificar roles
    public function hasRole($role)
    {
        return $this->rol === $role;
    }
}