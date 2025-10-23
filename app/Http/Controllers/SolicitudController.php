<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Historial_estados;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SolicitudController extends Controller
{
    /**
     * Mostrar todas las solicitudes del usuario autenticado
     */
    public function misSolicitudes()
    {
        $user = Auth::user();
        $solicitudes = Solicitud::where('id_usuario_creador', $user->id_usuario)
            ->whereNull('deleted_at') // Excluir anuladas si usas soft deletes
            ->orderBy('fecha_creacion', 'desc')
            ->get();

        return view('solicitudes.index', compact('solicitudes'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
{
    $unidades = \App\Models\Unidad::orderBy('nombre')->get();
    return view('solicitudes.create', compact('unidades'));
}

    /**
     * Guardar nueva solicitud
     */
    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => ['required', 'string'],
            'justificacion' => ['required', 'string'],
            'monto_total_estimado' => ['nullable', 'numeric', 'min:0'],
            'prioridad' => ['required', 'in:Baja,Media,Alta,Urgente'],
            'fecha_limitie' => ['nullable', 'date', 'after:today'],
            'id_unida_solicitante' => ['required', 'exists:UNIDAD,id_unidad'],
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            
            // Generar número de solicitud único
            $numeroSolicitud = 'SOL-' . date('Y') . '-' . str_pad(
                Solicitud::whereYear('fecha_creacion', date('Y'))->count() + 1,
                6,
                '0',
                STR_PAD_LEFT
            );

            // Obtener el siguiente ID
            $nextId = DB::table('SOLICITUD')->max('id_solicitud') + 1;

            $solicitud = Solicitud::create([
                'id_solicitud' => $nextId,
                'numero_solicitud' => $numeroSolicitud,
                'fecha_creacion' => now(),
                'descripcion' => $request->descripcion,
                'justificacion' => $request->justificacion,
                'estado' => 'Pendiente',
                'id_unida_solicitante' => $request->id_unida_solicitante,
                'id_usuario_creador' => $user->id_usuario,
                'prioridad' => $request->prioridad,
                'fecha_limitie' => $request->fecha_limitie,
                'monto_total_estimado' => $request->monto_total_estimado,
            ]);

            // Registrar en historial
            $this->registrarHistorial($solicitud, null, 'Pendiente', 'Solicitud creada');

            DB::commit();
            return redirect()->route('solicitudes.mias')->with('success', 'Solicitud creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la solicitud: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Mostrar detalle de una solicitud
     */
    public function show(Solicitud $solicitud)
    {
        // Cargar relaciones necesarias
        $solicitud->load([
            'usuarioCreador',
            'unidadSolicitante',
            'detalles',
            'historialEstados.usuario',
            'documentos',
            'aprobaciones.usuario'
        ]);

        return view('solicitudes.show', compact('solicitud'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Solicitud $solicitud)
    {
        // Solo puede editar el creador y si está en estado Pendiente o Rechazada
        if ($solicitud->id_usuario_creador != Auth::id() || 
            !in_array($solicitud->estado, ['Pendiente', 'Rechazada'])) {
            abort(403, 'No tiene permisos para editar esta solicitud.');
        }

        return view('solicitudes.edit', compact('solicitud'));
    }

    /**
     * Actualizar solicitud
     */
    public function update(Request $request, Solicitud $solicitud)
    {
        // Validar permisos
        if ($solicitud->id_usuario_creador != Auth::id() || 
            !in_array($solicitud->estado, ['Pendiente', 'Rechazada'])) {
            abort(403, 'No tiene permisos para editar esta solicitud.');
        }

        $request->validate([
            'descripcion' => ['required', 'string'],
            'justificacion' => ['required', 'string'],
            'monto_total_estimado' => ['nullable', 'numeric', 'min:0'],
            'prioridad' => ['required', 'in:Baja,Media,Alta,Urgente'],
            'fecha_limitie' => ['nullable', 'date', 'after:today'],
        ]);

        DB::beginTransaction();
        try {
            $solicitud->update($request->only([
                'descripcion',
                'justificacion',
                'monto_total_estimado',
                'prioridad',
                'fecha_limitie'
            ]));

            // Registrar cambio en historial
            $this->registrarHistorial(
                $solicitud,
                $solicitud->estado,
                $solicitud->estado,
                'Solicitud actualizada'
            );

            DB::commit();
            return redirect()->route('solicitudes.show', $solicitud)
                ->with('success', 'Solicitud actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Anular solicitud (soft delete lógico)
     */
    public function anular(Request $request, Solicitud $solicitud)
    {
        // Solo puede anular el creador
        if ($solicitud->id_usuario_creador != Auth::id()) {
            abort(403, 'No tiene permisos para anular esta solicitud.');
        }

        // No se puede anular si ya está completada o cerrada
        if (in_array($solicitud->estado, ['Completada', 'Finalizada'])) {
            return back()->with('error', 'No se puede anular una solicitud completada.');
        }

        $request->validate([
            'motivo_anulacion' => ['required', 'string', 'min:10']
        ]);

        DB::beginTransaction();
        try {
            $estadoAnterior = $solicitud->estado;
            $solicitud->update(['estado' => 'Anulada']);

            // Registrar en historial
            $this->registrarHistorial(
                $solicitud,
                $estadoAnterior,
                'Anulada',
                'Anulada por usuario. Motivo: ' . $request->motivo_anulacion
            );

            DB::commit();
            return redirect()->route('solicitudes.mias')
                ->with('success', 'Solicitud anulada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular: ' . $e->getMessage());
        }
    }

    /**
     * Reabrir solicitud anulada o rechazada
     */
    public function reabrir(Request $request, Solicitud $solicitud)
    {
        // Solo puede reabrir el creador
        if ($solicitud->id_usuario_creador != Auth::id()) {
            abort(403, 'No tiene permisos para reabrir esta solicitud.');
        }

        // Solo se puede reabrir si está Anulada o Rechazada
        if (!in_array($solicitud->estado, ['Anulada', 'Rechazada'])) {
            return back()->with('error', 'Solo se pueden reabrir solicitudes anuladas o rechazadas.');
        }

        DB::beginTransaction();
        try {
            $estadoAnterior = $solicitud->estado;
            $solicitud->update(['estado' => 'Pendiente']);

            // Registrar en historial
            $this->registrarHistorial(
                $solicitud,
                $estadoAnterior,
                'Pendiente',
                'Solicitud reabierta por usuario'
            );

            DB::commit();
            return redirect()->route('solicitudes.show', $solicitud)
                ->with('success', 'Solicitud reabierta correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al reabrir: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar historial de cambios de estado
     */
    public function historial(Solicitud $solicitud)
    {
        $historial = $solicitud->historialEstados()
            ->with('usuario')
            ->orderBy('fecha_cambio', 'desc')
            ->get();

        return view('solicitudes.historial', compact('solicitud', 'historial'));
    }

    /**
     * Método privado para registrar cambios en el historial
     */
    private function registrarHistorial(
        Solicitud $solicitud,
        ?string $estadoAnterior,
        string $estadoNuevo,
        ?string $observaciones = null
    ) {
        $nextId = DB::table('HISTORIAL_ESTADOS')->max('id_historial') + 1;

        Historial_estados::create([
            'id_historial' => $nextId,
            'id_solicitud' => $solicitud->id_solicitud,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'fecha_cambio' => now(),
            'id_usuario' => Auth::id(),
            'observaciones' => $observaciones,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Cambiar estado de la solicitud (para administradores/aprobadores)
     */
    public function cambiarEstado(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'nuevo_estado' => ['required', 'in:En Revisión,Aprobada,Rechazada,En Proceso,Completada'],
            'observaciones' => ['nullable', 'string']
        ]);

        DB::beginTransaction();
        try {
            $estadoAnterior = $solicitud->estado;
            $solicitud->update(['estado' => $request->nuevo_estado]);

            $this->registrarHistorial(
                $solicitud,
                $estadoAnterior,
                $request->nuevo_estado,
                $request->observaciones
            );

            DB::commit();
            return back()->with('success', 'Estado actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }
}