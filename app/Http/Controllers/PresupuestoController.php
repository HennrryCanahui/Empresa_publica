<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Presupuesto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresupuestoController extends Controller
{
    /**
     * Mostrar solicitudes pendientes de validación presupuestaria
     */
    public function index()
    {
        $user = Auth::user();
        
        // Verificar rol
        if (!in_array($user->rol, ['Presupuesto', 'Admin'])) {
            abort(403, 'No tiene permisos para acceder a esta sección.');
        }

        $solicitudes = Solicitud::with(['unidadSolicitante', 'usuarioCreador', 'detalles'])
            ->where('estado', 'En_Presupuesto')
            ->orderByRaw("CASE prioridad 
                WHEN 'Urgente' THEN 1 
                WHEN 'Alta' THEN 2 
                WHEN 'Media' THEN 3 
                WHEN 'Baja' THEN 4 
                END")
            ->orderBy('fecha_creacion', 'asc')
            ->paginate(15);

        return view('presupuesto.index', compact('solicitudes'));
    }

    /**
     * Mostrar formulario de validación presupuestaria
     */
    public function validar($id)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Presupuesto', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $solicitud = Solicitud::with(['unidadSolicitante', 'usuarioCreador', 'detalles.producto'])
            ->findOrFail($id);

        if ($solicitud->estado != 'En_Presupuesto') {
            return redirect()->route('presupuesto.index')
                ->with('error', 'La solicitud no está en estado de validación presupuestaria.');
        }

        return view('presupuesto.validar', compact('solicitud'));
    }

    /**
     * Procesar validación presupuestaria
     */
    public function procesarValidacion(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Presupuesto', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $validated = $request->validate([
            'monto_estimado' => 'required|numeric|min:0',
            'partida_presupuestaria' => 'required|string|max:100',
            'disponibilidad_actual' => 'required|numeric|min:0',
            'validacion' => 'required|in:Válido,Requiere_Ajuste,Rechazado',
            'observaciones' => 'nullable|string|max:4000',
        ]);

        $solicitud = Solicitud::findOrFail($id);

        if ($solicitud->estado != 'En_Presupuesto') {
            return back()->with('error', 'La solicitud no está en estado de validación.');
        }

        DB::beginTransaction();
        try {
            // Crear registro de presupuesto
            Presupuesto::create([
                'id_solicitud' => $solicitud->id_solicitud,
                'monto_estimado' => $validated['monto_estimado'],
                'partida_presupuestaria' => $validated['partida_presupuestaria'],
                'disponibilidad_actual' => $validated['disponibilidad_actual'],
                'validacion' => $validated['validacion'],
                'observaciones' => $validated['observaciones'],
                'fecha_revision' => now(),
                'id_usuario_presupuesto' => $user->id_usuario,
            ]);

            // Cambiar estado de solicitud según validación
            $nuevoEstado = match($validated['validacion']) {
                'Válido' => 'Presupuestada',
                'Rechazado' => 'Rechazada',
                'Requiere_Ajuste' => 'En_Presupuesto',
                default => 'En_Presupuesto'
            };

            $solicitud->update(['estado' => $nuevoEstado]);

            DB::commit();
            
            $mensaje = $validated['validacion'] == 'Válido' 
                ? 'Presupuesto validado exitosamente. Solicitud enviada a Compras.'
                : 'Validación presupuestaria registrada.';

            return redirect()->route('presupuesto.index')
                ->with('success', $mensaje);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al procesar validación: ' . $e->getMessage());
        }
    }

    /**
     * Ver historial de validaciones
     */
    public function historial()
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Presupuesto', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $validaciones = Presupuesto::with(['solicitud.usuarioCreador', 'usuarioPresupuesto'])
            ->orderBy('fecha_revision', 'desc')
            ->paginate(20);

        return view('presupuesto.historial', compact('validaciones'));
    }

    /**
     * Ver detalle de una validación
     */
    public function ver($id)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Presupuesto', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $presupuesto = Presupuesto::with([
            'solicitud.unidadSolicitante',
            'solicitud.usuarioCreador',
            'solicitud.detalles.producto',
            'usuarioPresupuesto'
        ])->findOrFail($id);

        return view('presupuesto.ver', compact('presupuesto'));
    }
}
