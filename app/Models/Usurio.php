<?php

// app/Models/Usuario.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;
    
    protected $table = 'usuario'; // Oracle en mayúsculas
    protected $primaryKey = 'id_usuario';
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
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
    
    // Laravel espera 'password', pero Oracle tiene 'contrasena'
    public function getAuthPassword()
    {
        return $this->contrasena;
    }
    
    // MÉTODOS DE ROLES PERSONALIZADOS
    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->rol, $role);
        }
        return $this->rol === $role;
    }
    
    public function hasAnyRole($roles)
    {
        return in_array($this->rol, $roles);
    }
    
    public function isAdmin()
    {
        return $this->rol === 'Admin';
    }
    
    public function isPresupuesto()
    {
        return $this->rol === 'Presupuesto';
    }
    
    public function isCompras()
    {
        return $this->rol === 'Compras';
    }
    
    public function isAutoridad()
    {
        return $this->rol === 'Autoridad';
    }
    
    public function isSolicitante()
    {
        return $this->rol === 'Solicitante';
    }
    
    // MÉTODOS DE PERMISOS PERSONALIZADOS
    public function can($ability, $arguments = [])
    {
        // Admin puede todo
        if ($this->isAdmin()) {
            return true;
        }
        
        // Definir permisos por rol
        $permisos = [
            'Solicitante' => [
                'solicitud.crear',
                'solicitud.ver.propias',
                'solicitud.editar.propias',
                'documento.subir',
            ],
            'Presupuesto' => [
                'solicitud.ver.todas',
                'presupuesto.validar',
                'presupuesto.crear',
                'presupuesto.rechazar',
            ],
            'Compras' => [
                'solicitud.ver.todas',
                'cotizacion.crear',
                'cotizacion.ver',
                'cotizacion.seleccionar',
                'proveedor.crear',
                'proveedor.editar',
                'orden.crear',
                'adquisicion.gestionar',
            ],
            'Autoridad' => [
                'solicitud.ver.todas',
                'aprobacion.crear',
                'aprobacion.rechazar',
                'cotizacion.ver',
            ],
        ];
        
        return isset($permisos[$this->rol]) && in_array($ability, $permisos[$this->rol]);
    }
    
    // Relaciones
    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad', 'id_unidad');
    }
    
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'id_usuario_creador', 'id_usuario');
    }
}