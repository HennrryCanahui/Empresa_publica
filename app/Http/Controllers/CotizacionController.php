<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Cotizacion;
use App\Models\Detalle_Cotizacion;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CotizacionController extends Controller
{
    /**
     * Mostrar solicitudes pendientes de cotización
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Compras', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $solicitudes = Solicitud::with(['unidadSolicitante', 'usuarioCreador', 'cotizaciones'])
            ->whereIn('estado', ['Presupuestada', 'En_Cotizacion'])
            ->orderByRaw("CASE prioridad 
                WHEN 'Urgente' THEN 1 
                WHEN 'Alta' THEN 2 
                WHEN 'Media' THEN 3 
                WHEN 'Baja' THEN 4 
                END")
            ->orderBy('fecha_creacion', 'asc')
            ->paginate(15);

        return view('cotizaciones.index', compact('solicitudes'));
    }

    /**
     * Mostrar formulario para crear cotización
     */
    public function create($id_solicitud)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Compras', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $solicitud = Solicitud::with(['detalles.producto', 'unidadSolicitante'])
            ->findOrFail($id_solicitud);

        if (!in_array($solicitud->estado, ['Presupuestada', 'En_Cotizacion'])) {
            return redirect()->route('cotizaciones.index')
                ->with('error', 'La solicitud no está disponible para cotización.');
        }

        $proveedores = Proveedor::where('activo', 1)
            ->orderBy('razon_social')
            ->get();

        return view('cotizaciones.create', compact('solicitud', 'proveedores'));
    }

    /**
     * Guardar nueva cotización
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Compras', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $validated = $request->validate([
            'id_solicitud' => 'required|exists:solicitud,id_solicitud',
            'id_proveedor' => 'required|exists:proveedor,id_proveedor',
            'fecha_cotizacion' => 'required|date',
            'fecha_validez' => 'nullable|date|after:fecha_cotizacion',
            'tiempo_entrega_dias' => 'nullable|integer|min:1',
            'condiciones_pago' => 'nullable|string|max:4000',
            'observaciones' => 'nullable|string|max:4000',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:catalogo_producto,id_producto',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Generar número de cotización
            $year = date('Y');
            $count = Cotizacion::whereYear('created_at', $year)->count() + 1;
            $numeroCotizacion = 'COT-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

            // Calcular monto total
            $montoTotal = 0;
            foreach ($validated['productos'] as $producto) {
                $montoTotal += $producto['cantidad'] * $producto['precio_unitario'];
            }

            // Crear cotización
            $cotizacion = Cotizacion::create([
                'numero_cotizacion' => $numeroCotizacion,
                'id_solicitud' => $validated['id_solicitud'],
                'id_proveedor' => $validated['id_proveedor'],
                'monto_total' => $montoTotal,
                'fecha_cotizacion' => $validated['fecha_cotizacion'],
                'fecha_validez' => $validated['fecha_validez'],
                'tiempo_entrega_dias' => $validated['tiempo_entrega_dias'],
                'condiciones_pago' => $validated['condiciones_pago'],
                'id_usuario_compras' => $user->id_usuario,
                'estado' => 'Activa',
                'observaciones' => $validated['observaciones'],
            ]);

            // Crear detalles de cotización
            foreach ($validated['productos'] as $producto) {
                $precioTotal = $producto['cantidad'] * $producto['precio_unitario'];

                Detalle_Cotizacion::create([
                    'id_cotizacion' => $cotizacion->id_cotizacion,
                    'id_producto' => $producto['id_producto'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio_unitario'],
                    'precio_total' => $precioTotal,
                    'observaciones' => $producto['observaciones'] ?? null,
                ]);
            }

            // Actualizar estado de solicitud
            $solicitud = Solicitud::find($validated['id_solicitud']);
            if ($solicitud->estado == 'Presupuestada') {
                $solicitud->update(['estado' => 'En_Cotizacion']);
            }

            DB::commit();
            
            return redirect()->route('cotizaciones.comparar', $validated['id_solicitud'])
                ->with('success', 'Cotización registrada exitosamente con número: ' . $numeroCotizacion);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al guardar cotización: ' . $e->getMessage());
        }
    }

    /**
     * Comparar cotizaciones de una solicitud
     */
    public function comparar($id_solicitud)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Compras', 'Autoridad', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $solicitud = Solicitud::with([
            'cotizaciones' => function($query) {
                $query->with(['proveedor', 'detalles.producto'])
                      ->orderBy('monto_total', 'asc');
            },
            'detalles.producto'
        ])->findOrFail($id_solicitud);

        return view('cotizaciones.comparar', compact('solicitud'));
    }

    /**
     * Seleccionar cotización ganadora
     */
    public function seleccionar(Request $request, $id_cotizacion)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Compras', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $validated = $request->validate([
            'justificacion' => 'required|string|min:20|max:4000',
        ]);

        DB::beginTransaction();
        try {
            $cotizacion = Cotizacion::with('solicitud')->findOrFail($id_cotizacion);

            // Marcar esta como seleccionada
            $cotizacion->update([
                'estado' => 'Seleccionada',
                'observaciones' => $validated['justificacion']
            ]);

            // Marcar las demás como descartadas
            Cotizacion::where('id_solicitud', $cotizacion->id_solicitud)
                ->where('id_cotizacion', '!=', $id_cotizacion)
                ->where('estado', 'Activa')
                ->update(['estado' => 'Descartada']);

            // Cambiar estado de solicitud a Cotizada
            $cotizacion->solicitud->update(['estado' => 'Cotizada']);

            DB::commit();
            
            return redirect()->route('cotizaciones.comparar', $cotizacion->id_solicitud)
                ->with('success', 'Cotización seleccionada exitosamente. Puede proceder a enviar a aprobación.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al seleccionar cotización: ' . $e->getMessage());
        }
    }

    /**
     * Enviar solicitud a aprobación después de seleccionar cotización
     */
    public function enviarAAprobacion($id_solicitud)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Compras', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $solicitud = Solicitud::findOrFail($id_solicitud);

        if ($solicitud->estado != 'Cotizada') {
            return back()->with('error', 'La solicitud debe tener una cotización seleccionada.');
        }

        // Verificar que tenga al menos una cotización seleccionada
        $cotizacionSeleccionada = Cotizacion::where('id_solicitud', $id_solicitud)
            ->where('estado', 'Seleccionada')
            ->exists();

        if (!$cotizacionSeleccionada) {
            return back()->with('error', 'Debe seleccionar una cotización antes de enviar a aprobación.');
        }

        DB::beginTransaction();
        try {
            $solicitud->update(['estado' => 'En_Aprobacion']);

            DB::commit();
            
            return redirect()->route('cotizaciones.index')
                ->with('success', 'Solicitud enviada a Aprobación exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalle de una cotización
     */
    public function ver($id)
    {
        $user = Auth::user();
        
        if (!in_array($user->rol, ['Compras', 'Autoridad', 'Admin'])) {
            abort(403, 'No tiene permisos.');
        }

        $cotizacion = Cotizacion::with([
            'solicitud.detalles.producto',
            'proveedor',
            'detalles.producto',
            'usuarioCompras'
        ])->findOrFail($id);

        return view('cotizaciones.ver', compact('cotizacion'));
    }
}
