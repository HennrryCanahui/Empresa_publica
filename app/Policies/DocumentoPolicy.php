<?php

namespace App\Policies;

use App\Models\DocumentoAdjunto;
use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentoPolicy
{
    use HandlesAuthorization;

    public function ver(Usuario $usuario, DocumentoAdjunto $documento)
    {
        // El creador de la solicitud, el usuario que subió el documento y roles administrativos lo pueden ver
        return $usuario->id_usuario === $documento->id_usuario_carga ||
               $usuario->id_usuario === $documento->solicitud->id_usuario_creador ||
               in_array($usuario->rol, ['Admin', 'Presupuesto', 'Compras', 'Autoridad']);
    }

    public function descargar(Usuario $usuario, DocumentoAdjunto $documento)
    {
        return $this->ver($usuario, $documento);
    }
}
