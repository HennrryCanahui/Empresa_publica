<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'USUARIO';
    protected $fillable = [
        'id_usuario',
        'nombre',
        'apellido',
        'correo',
        'contrasena',
        'rol',
        'id_unidad',
        'telefono',
        'activo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // Primary key configuration
    protected $primaryKey = 'id_usuario';
    protected $keyType = 'int';
    public $incrementing = false;

    public $timestamps = true;

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'ultimo_acceso' => 'datetime',
        'activo' => 'boolean',
    ];

    // Relations
    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'id_usuario_creador', 'id_usuario');
    }

    /**
     * Get the password for the user (compatibility with custom column name).
     *
     * @return string|null
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    /**
     * Verifica si el usuario tiene un rol específico (comparación no sensible a mayúsculas/minúsculas).
     */
    public function hasRole(string $role): bool
    {
        $current = $this->rol ?? '';
        return strcasecmp($current, $role) === 0;
    }

    /**
     * Verifica si el usuario tiene alguno de los roles indicados.
     * Acepta array o string separado por comas. El rol "Admin" tiene acceso a todo.
     *
     * @param array<string>|string $roles
     */
    public function hasAnyRole(array|string $roles): bool
    {
        // Superusuario
        if (strcasecmp($this->rol ?? '', 'Admin') === 0) {
            return true;
        }

        if (is_string($roles)) {
            // Permitir cadena "Rol1,Rol2"
            $roles = array_map('trim', explode(',', $roles));
        }

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }
}
