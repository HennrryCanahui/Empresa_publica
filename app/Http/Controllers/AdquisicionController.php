<?php

namespace App\Http\Controllers;

use App\Models\Adquisicion;
use App\Models\Solicitud;
use App\Models\Cotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdquisicionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!in_array(Auth::user()->rol, ['Compras', 'Admin'])) {
                return redirect()->route('dashboard')
                    ->with('error', 'No tiene permisos para acceder a esta sección.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $adquisiciones = Adquisicion::with(['solicitud', 'proveedor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('adquisiciones.index', compact('adquisiciones'));
    }

    public function create(Solicitud $solicitud)
    {
        if ($solicitud->estado !== 'Aprobada') {
            return redirect()->route('adquisiciones.index')
                ->with('error', 'La solicitud no está aprobada para generar orden de compra.');
        }

        $cotizacion = $solicitud->cotizaciones()
            ->where('estado', 'Seleccionada')
            ->firstOrFail();

        return view('adquisiciones.create', compact('solicitud', 'cotizacion'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_solicitud' => 'required|exists:solicitud,id_solicitud',
            'id_cotizacion_seleccionada' => 'required|exists:cotizacion,id_cotizacion',
            'fecha_entrega_programada' => 'required|date|after:today',
            'observaciones' => 'nullable|string|max:4000',
            'documento' => 'nullable|file|mimes:pdf,doc,docx|max:5120'
        ]);

        DB::beginTransaction();
        try {
            $solicitud = Solicitud::findOrFail($request->id_solicitud);
            $cotizacion = Cotizacion::findOrFail($request->id_cotizacion_seleccionada);

            // Crear la adquisición
            $adquisicion = new Adquisicion([
                'id_solicitud' => $solicitud->id_solicitud,
                'id_cotizacion_seleccionada' => $cotizacion->id_cotizacion,
                'id_proveedor' => $cotizacion->id_proveedor,
                'monto_final' => $cotizacion->monto_total,
                'fecha_adquisicion' => now(),
                'estado_entrega' => 'Pendiente',
                'fecha_entrega_programada' => $request->fecha_entrega_programada,
                'observaciones' => $request->observaciones,
                'id_usuario_compras' => Auth::id()
            ]);

            // Generar número de orden de compra
            $contador = Adquisicion::whereYear('fecha_adquisicion', now()->year)->count() + 1;
            $adquisicion->numero_orden_compra = 'OC-' . now()->year . '-' . str_pad($contador, 5, '0', STR_PAD_LEFT);

            $adquisicion->save();

            // Actualizar estado de la solicitud
            $solicitud->cambiarEstado('En_Adquisicion', Auth::id(), 'Orden de compra generada');

            DB::commit();
            return redirect()->route('adquisiciones.index')
                ->with('success', 'Orden de compra generada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al generar la orden de compra: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function actualizarEstado(Request $request, Adquisicion $adquisicion)
    {
        $request->validate([
            'estado_entrega' => 'required|in:Pendiente,Parcial,Completa,Cancelada',
            'fecha_entrega_real' => 'required_if:estado_entrega,Completa|nullable|date',
            'numero_factura' => 'required_if:estado_entrega,Completa|nullable|string|max:100',
            'observaciones' => 'nullable|string|max:4000'
        ]);

        DB::beginTransaction();
        try {
            $adquisicion->update([
                'estado_entrega' => $request->estado_entrega,
                'fecha_entrega_real' => $request->fecha_entrega_real,
                'numero_factura' => $request->numero_factura,
                'observaciones' => $request->observaciones ?: $adquisicion->observaciones
            ]);

            // Si la entrega está completa, actualizar estado de la solicitud
            if ($request->estado_entrega === 'Completa') {
                $adquisicion->solicitud->cambiarEstado('Completada', Auth::id(), 'Entrega completada');
            }

            DB::commit();
            return redirect()->route('adquisiciones.index')
                ->with('success', 'Estado de la adquisición actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al actualizar el estado: ' . $e->getMessage())
                ->withInput();
        }
    }
}