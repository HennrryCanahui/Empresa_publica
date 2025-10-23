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
}
