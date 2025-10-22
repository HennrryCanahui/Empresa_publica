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
               in_array($usuario->rol, ['admin', 'presupuesto', 'compras', 'aprobador']);
    }

    /**
     * Determine si el usuario puede crear solicitudes.
     */
    public function create(Usuario $usuario)
    {
        return $usuario->rol === 'solicitante';
    }

    /**
     * Determine si el usuario puede actualizar la solicitud.
     */
    public function update(Usuario $usuario, Solicitud $solicitud)
    {
        if ($usuario->rol === 'solicitante') {
            return $usuario->id_usuario === $solicitud->id_usuario_creador && 
                   $solicitud->estado === 'pendiente';
        }

        return in_array($usuario->rol, ['admin', 'presupuesto', 'compras', 'aprobador']);
    }

    /**
     * Determine si el usuario puede eliminar la solicitud.
     */
    public function delete(Usuario $usuario, Solicitud $solicitud)
    {
        return $usuario->rol === 'admin' || 
               ($usuario->id_usuario === $solicitud->id_usuario_creador && 
                $solicitud->estado === 'pendiente');
    }

    /**
     * Determine si el usuario puede validar el presupuesto de la solicitud.
     */
    public function validarPresupuesto(Usuario $usuario, Solicitud $solicitud)
    {
        return $usuario->rol === 'presupuesto' && 
               $solicitud->estado === 'pendiente' && 
               is_null($solicitud->validacion_presupuesto);
    }

    /**
     * Determine si el usuario puede gestionar las cotizaciones de la solicitud.
     */
    public function gestionarCotizaciones(Usuario $usuario, Solicitud $solicitud)
    {
        return $usuario->rol === 'compras' && 
               $solicitud->estado === 'en_proceso' && 
               !is_null($solicitud->validacion_presupuesto) && 
               is_null($solicitud->cotizacion_seleccionada);
    }

    /**
     * Determine si el usuario puede aprobar la solicitud.
     */
    public function aprobar(Usuario $usuario, Solicitud $solicitud)
    {
        return $usuario->rol === 'aprobador' && 
               $solicitud->estado === 'en_proceso' && 
               !is_null($solicitud->cotizacion_seleccionada) && 
               is_null($solicitud->aprobacion_final);
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