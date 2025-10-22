<?php

namespace App\Http\Controllers;

use App\Models\Aprobacion;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AprobacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!in_array(Auth::user()->rol, ['Autoridad', 'Admin'])) {
                return redirect()->route('dashboard')
                    ->with('error', 'No tiene permisos para acceder a esta sección.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $solicitudes = Solicitud::where('estado', 'En_Aprobacion')
            ->with(['unidadSolicitante', 'usuarioCreador', 'presupuesto'])
            ->orderBy('fecha_creacion', 'asc')
            ->paginate(10);

        return view('aprobaciones.index', compact('solicitudes'));
    }

    public function evaluar(Solicitud $solicitud)
    {
        if ($solicitud->estado !== 'En_Aprobacion') {
            return redirect()->route('aprobaciones.index')
                ->with('error', 'La solicitud no está en estado para aprobación.');
        }

        $solicitud->load(['detalles.producto', 'presupuesto', 'cotizaciones' => function($query) {
            $query->where('estado', 'Seleccionada');
        }]);

        return view('aprobaciones.evaluar', compact('solicitud'));
    }

    public function procesarEvaluacion(Request $request, Solicitud $solicitud)
    {
        if ($solicitud->estado !== 'En_Aprobacion') {
            return redirect()->route('aprobaciones.index')
                ->with('error', 'La solicitud no está en estado para aprobación.');
        }

        $request->validate([
            'decision' => 'required|in:Aprobada,Rechazada,Requiere_Revision',
            'monto_aprobado' => 'required_if:decision,Aprobada|nullable|numeric|min:0',
            'condiciones_aprobacion' => 'nullable|string|max:4000',
            'observaciones' => 'required|string|max:4000'
        ]);

        DB::beginTransaction();
        try {
            // Crear registro de aprobación
            $aprobacion = new Aprobacion([
                'id_solicitud' => $solicitud->id_solicitud,
                'decision' => $request->decision,
                'observaciones' => $request->observaciones,
                'id_usuario_autoridad' => Auth::id(),
                'monto_aprobado' => $request->monto_aprobado,
                'condiciones_aprobacion' => $request->condiciones_aprobacion
            ]);
            $aprobacion->save();

            // Actualizar estado de la solicitud según la decisión
            $nuevoEstado = $request->decision === 'Aprobada' ? 'Aprobada' : 
                         ($request->decision === 'Rechazada' ? 'Rechazada' : 'En_Aprobacion');
            
            $solicitud->cambiarEstado($nuevoEstado, Auth::id(), $request->observaciones);

            DB::commit();
            return redirect()->route('aprobaciones.index')
                ->with('success', 'Evaluación procesada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al procesar la evaluación: ' . $e->getMessage())
                ->withInput();
        }
    }
}