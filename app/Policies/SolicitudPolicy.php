<?php

namespace App\Policies;

use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class SolicitudPolicy
{
    use HandlesAuthorization;

    /**
     * Determine si el usuario puede ver el listado de solicitudes.
     */
    public function viewAny(Usuario $usuario)
    {
        return true; // Todos los usuarios pueden ver el listado de solicitudes
    }

    /**
     * Determine si el usuario puede ver la solicitud.
     */
    public function view(Usuario $usuario, Solicitud $solicitud)
    {
        return $usuario->id_usuario === $solicitud->id_usuario_creador || 
               in_array($usuario->rol, ['Admin', 'Presupuesto', 'Compras', 'Autoridad']);
    }

    /**
     * Determine si el usuario puede crear solicitudes.
     */
    public function create(Usuario $usuario)
    {
        return $usuario->rol === 'Solicitante';
    }

    /**
     * Determine si el usuario puede actualizar la solicitud.
     */
    public function update(Usuario $usuario, Solicitud $solicitud)
    {
        if ($usuario->rol === 'Solicitante') {
            return $usuario->id_usuario === $solicitud->id_usuario_creador && 
                   in_array($solicitud->estado, ['Creada', 'En_Presupuesto']);
        }

        return in_array($usuario->rol, ['Admin', 'Presupuesto', 'Compras', 'Autoridad']);
    }

    /**
     * Determine si el usuario puede eliminar la solicitud.
     */
    public function delete(Usuario $usuario, Solicitud $solicitud)
    {
        return $usuario->rol === 'Admin' || 
               ($usuario->id_usuario === $solicitud->id_usuario_creador && 
                $solicitud->estado === 'Creada');
    }

    /**
     * Determine si el usuario puede validar el presupuesto de la solicitud.
     */
    public function validarPresupuesto(Usuario $usuario, Solicitud $solicitud)
    {
        return $usuario->rol === 'Presupuesto' && 
               $solicitud->estado === 'En_Presupuesto' && 
               is_null($solicitud->presupuesto);
    }

    /**
     * Determine si el usuario puede gestionar las cotizaciones de la solicitud.
     */
    public function gestionarCotizaciones(Usuario $usuario, Solicitud $solicitud)
    {
        return $usuario->rol === 'Compras' && 
               in_array($solicitud->estado, ['Presupuestada', 'En_Cotizacion']) && 
               !is_null($solicitud->presupuesto) && 
               $solicitud->cotizaciones()->count() >= 0;
    }

    /**
     * Determine si el usuario puede aprobar la solicitud.
     */
    public function aprobar(Usuario $usuario, Solicitud $solicitud)
    {
        return $usuario->rol === 'Autoridad' && 
               $solicitud->estado === 'En_Aprobacion' && 
               $solicitud->cotizaciones()->where('estado', 'Seleccionada')->exists() && 
               is_null($solicitud->aprobacion);
    }

    /**
     * Determine si el usuario puede ver los documentos adjuntos de la solicitud.
     */
    public function verDocumentos(Usuario $usuario, Solicitud $solicitud)
    {
        return $usuario->id_usuario === $solicitud->id_usuario_creador || 
               in_array($usuario->rol, ['admin', 'presupuesto', 'compras', 'aprobador']);
    }
}