<?php

namespace App\Http\Controllers;

use App\Models\Presupuesto;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresupuestoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!in_array(Auth::user()->rol, ['Presupuesto', 'Admin'])) {
                return redirect()->route('dashboard')
                    ->with('error', 'No tiene permisos para acceder a esta sección.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $solicitudes = Solicitud::where('estado', 'En_Presupuesto')
            ->orderBy('fecha_creacion', 'asc')
            ->paginate(10);

        return view('presupuestos.index', compact('solicitudes'));
    }

    public function validar(Solicitud $solicitud)
    {
        if ($solicitud->estado !== 'En_Presupuesto') {
            return redirect()->route('presupuestos.index')
                ->with('error', 'La solicitud no está en estado de validación presupuestaria.');
        }

        return view('presupuestos.validar', compact('solicitud'));
    }

    public function procesarValidacion(Request $request, Solicitud $solicitud)
    {
        if ($solicitud->estado !== 'En_Presupuesto') {
            return redirect()->route('presupuestos.index')
                ->with('error', 'La solicitud no está en estado de validación presupuestaria.');
        }

        $request->validate([
            'monto_estimado' => 'required|numeric|min:0',
            'partida_presupuestaria' => 'required|string|max:100',
            'disponibilidad_actual' => 'required|numeric|min:0',
            'validacion' => 'required|in:Válido,Requiere_Ajuste,Rechazado',
            'observaciones' => 'required|string|max:4000'
        ]);

        DB::beginTransaction();
        try {
            // Crear registro de presupuesto
            $presupuesto = new Presupuesto([
                'id_solicitud' => $solicitud->id_solicitud,
                'monto_estimado' => $request->monto_estimado,
                'partida_presupuestaria' => $request->partida_presupuestaria,
                'disponibilidad_actual' => $request->disponibilidad_actual,
                'validacion' => $request->validacion,
                'observaciones' => $request->observaciones,
                'id_usuario_presupuesto' => Auth::id()
            ]);
            $presupuesto->save();

            // Actualizar estado de la solicitud según la validación
            $nuevoEstado = $request->validacion === 'Válido' ? 'Presupuestada' : 
                          ($request->validacion === 'Rechazado' ? 'Rechazada' : 'En_Presupuesto');
            
            $solicitud->cambiarEstado($nuevoEstado, Auth::id(), $request->observaciones);

            DB::commit();
            return redirect()->route('presupuestos.index')
                ->with('success', 'Validación presupuestaria procesada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al procesar la validación: ' . $e->getMessage())
                ->withInput();
        }
    }
}