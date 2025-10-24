<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Aprobacion;
use App\Models\Cotizacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AprobacionController extends Controller
{
    /**
     * Mostrar solicitudes pendientes de aprobación
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Autoridad', 'Admin'])) {
            abort(403, 'No tiene permisos para acceder a esta sección.');
        }

        $solicitudes = Solicitud::with([
            'unidadSolicitante',
            'usuarioCreador',
            'cotizaciones' => function($query) {
                $query->where('estado', 'Seleccionada')->with('proveedor');
            },
            'presupuesto'
        ])
        ->where('estado', 'En_Aprobacion')
        ->orderByRaw("CASE prioridad 
            WHEN 'Urgente' THEN 1 
            WHEN 'Alta' THEN 2 
            WHEN 'Media' THEN 3 
            WHEN 'Baja' THEN 4 
            END")
        ->orderBy('fecha_creacion', 'asc')
        ->paginate(15);

        return view('aprobacion.index', compact('solicitudes'));
    }

    /**
     * Mostrar formulario de aprobación/rechazo
     */
    public function revisar($id)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Autoridad', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $solicitud = Solicitud::with([
            'unidadSolicitante',
            'usuarioCreador',
            'detalles.producto',
            'presupuesto.usuarioPresupuesto',
            'cotizaciones' => function($query) {
                $query->where('estado', 'Seleccionada')
                      ->with(['proveedor', 'detalles.producto']);
            },
            'historialEstados.usuario'
        ])->findOrFail($id);

        if ($solicitud->estado != 'En_Aprobacion') {
            return redirect()->route('aprobacion.index')
                ->with('error', 'La solicitud no está pendiente de aprobación.');
        }

        return view('aprobacion.revisar', compact('solicitud'));
    }

    /**
     * Procesar aprobación o rechazo
     */
    public function procesar(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Autoridad', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $validated = $request->validate([
            'decision' => 'required|in:Aprobada,Rechazada,Requiere_Revision',
            'monto_aprobado' => 'nullable|numeric|min:0',
            'observaciones' => 'required|string|min:10|max:4000',
            'condiciones_aprobacion' => 'nullable|string|max:4000',
        ]);

        $solicitud = Solicitud::findOrFail($id);

        if ($solicitud->estado != 'En_Aprobacion') {
            return back()->with('error', 'La solicitud no está en estado de aprobación.');
        }

        DB::beginTransaction();
        try {
            // Crear registro de aprobación
            Aprobacion::create([
                'id_solicitud' => $solicitud->id_solicitud,
                'decision' => $validated['decision'],
                'observaciones' => $validated['observaciones'],
                'fecha_aprobacion' => now(),
                'id_usuario_autoridad' => $user->id_usuario,
                'monto_aprobado' => $validated['monto_aprobado'] ?? $solicitud->monto_total_estimado,
                'condiciones_aprobacion' => $validated['condiciones_aprobacion'],
            ]);

            // Cambiar estado de solicitud
            $nuevoEstado = $validated['decision'];
            $solicitud->update(['estado' => $nuevoEstado]);

            DB::commit();
            
            $mensaje = match($validated['decision']) {
                'Aprobada' => 'Solicitud aprobada exitosamente. Puede proceder a adquisición.',
                'Rechazada' => 'Solicitud rechazada.',
                'Requiere_Revision' => 'Solicitud marcada para revisión.',
                default => 'Decisión registrada.'
            };

            return redirect()->route('aprobacion.index')
                ->with('success', $mensaje);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al procesar aprobación: ' . $e->getMessage());
        }
    }

    /**
     * Ver historial de aprobaciones
     */
    public function historial()
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Autoridad', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $aprobaciones = Aprobacion::with([
            'solicitud.usuarioCreador',
            'solicitud.unidadSolicitante',
            'usuarioAutoridad'
        ])
        ->orderBy('fecha_aprobacion', 'desc')
        ->paginate(20);

        return view('aprobacion.historial', compact('aprobaciones'));
    }

    /**
     * Ver detalle de una aprobación
     */
    public function ver($id)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Autoridad', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $aprobacion = Aprobacion::with([
            'solicitud.unidadSolicitante',
            'solicitud.usuarioCreador',
            'solicitud.detalles.producto',
            'solicitud.presupuesto',
            'solicitud.cotizaciones' => function($query) {
                $query->where('estado', 'Seleccionada')->with('proveedor');
            },
            'usuarioAutoridad'
        ])->findOrFail($id);

        return view('aprobacion.ver', compact('aprobacion'));
    }
}
