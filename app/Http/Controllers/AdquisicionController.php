<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Adquisicion;
use App\Models\Cotizacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdquisicionController extends Controller
{
    /**
     * Mostrar solicitudes aprobadas pendientes de adquisición
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Compras', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $solicitudes = Solicitud::with([
            'unidadSolicitante',
            'usuarioCreador',
            'cotizaciones' => function($query) {
                $query->where('estado', 'Seleccionada')->with('proveedor');
            },
            'aprobacion',
            'adquisicion'
        ])
        ->whereIn('estado', ['Aprobada', 'En_Adquisicion'])
        ->orderBy('fecha_creacion', 'asc')
        ->paginate(15);

        return view('adquisiciones.index', compact('solicitudes'));
    }

    /**
     * Mostrar formulario para crear orden de compra
     */
    public function create($id_solicitud)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Compras', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $solicitud = Solicitud::with([
            'unidadSolicitante',
            'usuarioCreador',
            'detalles.producto',
            'presupuesto',
            'cotizaciones' => function($query) {
                $query->where('estado', 'Seleccionada')
                      ->with(['proveedor', 'detalles.producto']);
            },
            'aprobacion'
        ])->findOrFail($id_solicitud);

        if ($solicitud->estado != 'Aprobada') {
            return redirect()->route('adquisiciones.index')
                ->with('error', 'La solicitud debe estar aprobada para crear una orden de compra.');
        }

        $cotizacionSeleccionada = $solicitud->cotizaciones->first();

        return view('adquisiciones.create', compact('solicitud', 'cotizacionSeleccionada'));
    }

    /**
     * Guardar orden de compra
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Compras', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $validated = $request->validate([
            'id_solicitud' => 'required|exists:solicitud,id_solicitud',
            'id_cotizacion_seleccionada' => 'required|exists:cotizacion,id_cotizacion',
            'id_proveedor' => 'required|exists:proveedor,id_proveedor',
            'monto_final' => 'required|numeric|min:0',
            'fecha_adquisicion' => 'required|date',
            'fecha_entrega_programada' => 'required|date|after:fecha_adquisicion',
            'numero_factura' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string|max:4000',
        ]);

        $solicitud = Solicitud::findOrFail($validated['id_solicitud']);

        if ($solicitud->estado != 'Aprobada') {
            return back()->with('error', 'La solicitud debe estar aprobada.');
        }

        DB::beginTransaction();
        try {
            // Generar número de orden de compra
            $year = date('Y');
            $count = Adquisicion::whereYear('created_at', $year)->count() + 1;
            $numeroOrden = 'OC-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

            // Crear adquisición
            $adquisicion = Adquisicion::create([
                'numero_orden_compra' => $numeroOrden,
                'id_solicitud' => $validated['id_solicitud'],
                'id_cotizacion_seleccionada' => $validated['id_cotizacion_seleccionada'],
                'id_proveedor' => $validated['id_proveedor'],
                'numero_factura' => $validated['numero_factura'],
                'monto_final' => $validated['monto_final'],
                'fecha_adquisicion' => $validated['fecha_adquisicion'],
                'estado_entrega' => 'Pendiente',
                'fecha_entrega_programada' => $validated['fecha_entrega_programada'],
                'observaciones' => $validated['observaciones'],
                'id_usuario_compras' => $user->id_usuario,
            ]);

            // Cambiar estado de solicitud
            $solicitud->update(['estado' => 'En_Adquisicion']);

            DB::commit();
            
            return redirect()->route('adquisiciones.ver', $adquisicion->id_adquisicion)
                ->with('success', 'Orden de compra creada exitosamente: ' . $numeroOrden);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al crear orden de compra: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalle de una adquisición
     */
    public function ver($id)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Compras', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $adquisicion = Adquisicion::with([
            'solicitud.detalles.producto',
            'solicitud.unidadSolicitante',
            'solicitud.usuarioCreador',
            'cotizacionSeleccionada.detalles.producto',
            'proveedor',
            'usuarioCompras'
        ])->findOrFail($id);

        return view('adquisiciones.ver', compact('adquisicion'));
    }

    /**
     * Actualizar estado de entrega
     */
    public function actualizarEntrega(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Compras', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $validated = $request->validate([
            'estado_entrega' => 'required|in:Pendiente,Parcial,Completa,Cancelada',
            'fecha_entrega_real' => 'nullable|date',
            'numero_factura' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string|max:4000',
        ]);

        $adquisicion = Adquisicion::with('solicitud')->findOrFail($id);

        DB::beginTransaction();
        try {
            $adquisicion->update($validated);

            // Si la entrega está completa, cambiar estado de solicitud
            if ($validated['estado_entrega'] == 'Completa') {
                $adquisicion->solicitud->update(['estado' => 'Completada']);
            }

            DB::commit();
            
            return redirect()->route('adquisiciones.ver', $id)
                ->with('success', 'Estado de entrega actualizado exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Listar todas las adquisiciones completadas
     */
    public function historial()
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Compras', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $adquisiciones = Adquisicion::with([
            'solicitud.usuarioCreador',
            'proveedor',
            'usuarioCompras'
        ])
        ->orderBy('fecha_adquisicion', 'desc')
        ->paginate(20);

        return view('adquisiciones.historial', compact('adquisiciones'));
    }
}
