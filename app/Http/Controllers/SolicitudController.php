<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Detalle_solicitud;
use App\Models\Catalogo_producto;
use App\Models\Unidad;
use App\Models\Historial_estados;
use App\Models\Documento_adjunto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SolicitudController extends Controller
{
    /**
     * Mostrar todas las solicitudes del usuario autenticado
     */
    public function index()
    {
        $user = Auth::user();
        $solicitudes = Solicitud::with(['unidadSolicitante', 'usuarioCreador', 'detalles'])
            ->where('id_usuario_creador', $user->id_usuario)
            ->orderBy('fecha_creacion', 'desc')
            ->paginate(15);

        return view('solicitudes.index', compact('solicitudes'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $productos = Catalogo_producto::where('activo', 1)
            ->with('categoria')
            ->orderBy('nombre')
            ->get();
        
        $unidades = Unidad::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        return view('solicitudes.create', compact('productos', 'unidades'));
    }

    public function misSolicitudes()
{
    $user = Auth::user();
    $solicitudes = Solicitud::with(['unidadSolicitante', 'usuarioCreador', 'detalles'])
        ->where('id_usuario_creador', $user->id_usuario)
        ->orderBy('fecha_creacion', 'desc')
        ->paginate(15);

    return view('solicitudes.index', compact('solicitudes'));
}

    /**
     * Guardar nueva solicitud
     */
   public function store(Request $request)
{
    // Validación completa
    $validated = $request->validate([
        'descripcion' => 'required|string|max:4000',
        'justificacion' => 'required|string|max:4000',
        'prioridad' => 'required|in:Baja,Media,Alta,Urgente',
        'fecha_limite' => 'nullable|date|after:today',
        'id_unidad_solicitante' => 'required|exists:UNIDAD,id_unidad',
        'productos' => 'required|array|min:1',
        'productos.*.id_producto' => 'required|exists:CATALOGO_PRODUCTO,id_producto',
        'productos.*.cantidad' => 'required|numeric|min:0.01',
        'productos.*.especificaciones_adicionales' => 'nullable|string|max:4000',
    ], [
        'productos.required' => 'Debe agregar al menos un producto',
        'productos.*.id_producto.required' => 'Debe seleccionar un producto',
        'productos.*.cantidad.required' => 'Debe ingresar una cantidad',
        'productos.*.cantidad.min' => 'La cantidad debe ser mayor a 0',
    ]);

    DB::beginTransaction();
    try {
        $user = Auth::user();
        
        // Generar número de solicitud único
        $year = date('Y');
        $count = Solicitud::whereYear('fecha_creacion', $year)->count() + 1;
        $numeroSolicitud = 'SOL-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

        // Crear solicitud
        $solicitud = Solicitud::create([
            'numero_solicitud' => $numeroSolicitud,
            'fecha_creacion' => now(),
            'descripcion' => $validated['descripcion'],
            'justificacion' => $validated['justificacion'],
            'estado' => 'Creada',
            'id_unidad_solicitante' => $validated['id_unidad_solicitante'],
            'id_usuario_creador' => $user->id_usuario,
            'prioridad' => $validated['prioridad'],
            'fecha_limite' => $validated['fecha_limite'],
        ]);

        // Crear detalles de solicitud
        $montoTotal = 0;
        foreach ($validated['productos'] as $producto) {
            $catalogoProducto = Catalogo_producto::find($producto['id_producto']);
            
            if (!$catalogoProducto) {
                throw new \Exception("Producto no encontrado: {$producto['id_producto']}");
            }
            
            $precioEstimado = $catalogoProducto->precio_referencia ?? 0;
            $precioTotal = $producto['cantidad'] * $precioEstimado;
            $montoTotal += $precioTotal;

            Detalle_solicitud::create([
                'id_solicitud' => $solicitud->id_solicitud,
                'id_producto' => $producto['id_producto'],
                'cantidad' => $producto['cantidad'],
                'especificaciones_adicionales' => $producto['especificaciones_adicionales'] ?? null,
                'precio_estimado_unitario' => $precioEstimado,
                'precio_estimado_total' => $precioTotal,
            ]);
        }

        // Actualizar monto total
        $solicitud->update(['monto_total_estimado' => $montoTotal]);

        DB::commit();
        
        return redirect()->route('solicitudes.show', $solicitud->id_solicitud)
            ->with('success', 'Solicitud creada exitosamente con número: ' . $numeroSolicitud);
            
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error al crear solicitud: ' . $e->getMessage());
        return back()->withInput()
            ->with('error', 'Error al crear la solicitud: ' . $e->getMessage());
    }
}
    public function show($id)
    {
        $solicitud = Solicitud::with([
            'usuarioCreador',
            'unidadSolicitante',
            'detalles.producto.categoria',
            'historialEstados.usuario',
            'presupuesto.usuarioPresupuesto',
            'cotizaciones.proveedor',
            'cotizaciones.detalles',
            'aprobacion.usuarioAutoridad',
            'adquisicion'
        ])->findOrFail($id);

        // Verificar permisos: solo puede ver el creador o usuarios con roles especiales
        $user = Auth::user();
        if ($solicitud->id_usuario_creador != $user->id_usuario && 
            !in_array($user->rol, ['Presupuesto', 'Compras', 'Autoridad', 'Admin'])) {
            abort(403, 'No tiene permisos para ver esta solicitud.');
        }

        return view('solicitudes.show', compact('solicitud'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $solicitud = Solicitud::with('detalles.producto')->findOrFail($id);
        
        // Solo puede editar el creador y si está en estado Creada
        if ($solicitud->id_usuario_creador != Auth::user()->id_usuario || 
            !in_array($solicitud->estado, ['Creada', 'Rechazada'])) {
            abort(403, 'No tiene permisos para editar esta solicitud o ya está en proceso.');
        }

        $productos = Catalogo_producto::where('activo', 1)
            ->with('categoria')
            ->orderBy('nombre')
            ->get();
        
        $unidades = Unidad::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        return view('solicitudes.edit', compact('solicitud', 'productos', 'unidades'));
    }

    /**
     * Actualizar solicitud
     */
   public function update(Request $request, $id)
{
    $solicitud = Solicitud::findOrFail($id);
    
    // Validar permisos
    if ($solicitud->id_usuario_creador != Auth::user()->id_usuario || 
        !in_array($solicitud->estado, ['Creada', 'Rechazada'])) {
        abort(403, 'No tiene permisos para editar esta solicitud.');
    }

    $validated = $request->validate([
        'descripcion' => 'required|string|max:4000',
        'justificacion' => 'required|string|max:4000',
        'prioridad' => 'required|in:Baja,Media,Alta,Urgente',
        'fecha_limite' => 'nullable|date|after:today',
        'id_unidad_solicitante' => 'required|exists:UNIDAD,id_unidad',
        'productos' => 'required|array|min:1',
        'productos.*.id_producto' => 'required|exists:CATALOGO_PRODUCTO,id_producto',
        'productos.*.cantidad' => 'required|numeric|min:0.01',
        'productos.*.especificaciones_adicionales' => 'nullable|string|max:4000',
    ]);

    DB::beginTransaction();
    try {
        // Actualizar solicitud
        $solicitud->update([
            'descripcion' => $validated['descripcion'],
            'justificacion' => $validated['justificacion'],
            'prioridad' => $validated['prioridad'],
            'fecha_limite' => $validated['fecha_limite'],
            'id_unidad_solicitante' => $validated['id_unidad_solicitante'],
        ]);

        // Eliminar detalles anteriores
        Detalle_solicitud::where('id_solicitud', $solicitud->id_solicitud)->delete();

        // Crear nuevos detalles
        $montoTotal = 0;
        foreach ($validated['productos'] as $producto) {
            $catalogoProducto = Catalogo_producto::find($producto['id_producto']);
            $precioEstimado = $catalogoProducto->precio_referencia ?? 0;
            $precioTotal = $producto['cantidad'] * $precioEstimado;
            $montoTotal += $precioTotal;

            Detalle_solicitud::create([
                'id_solicitud' => $solicitud->id_solicitud,
                'id_producto' => $producto['id_producto'],
                'cantidad' => $producto['cantidad'],
                'especificaciones_adicionales' => $producto['especificaciones_adicionales'] ?? null,
                'precio_estimado_unitario' => $precioEstimado,
                'precio_estimado_total' => $precioTotal,
            ]);
        }

        // Actualizar monto total
        $solicitud->update(['monto_total_estimado' => $montoTotal]);

        DB::commit();
        return redirect()->route('solicitudes.show', $solicitud->id_solicitud)
            ->with('success', 'Solicitud actualizada exitosamente.');
            
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error al actualizar solicitud: ' . $e->getMessage());
        return back()->withInput()
            ->with('error', 'Error al actualizar: ' . $e->getMessage());
    }
}


    /**
     * Enviar solicitud a presupuesto
     */
    public function enviarAPresupuesto($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        
        // Solo el creador y si está en estado Creada
        if ($solicitud->id_usuario_creador != Auth::user()->id_usuario || 
            $solicitud->estado != 'Creada') {
            abort(403, 'No puede enviar esta solicitud.');
        }

        DB::beginTransaction();
        try {
            DB::table('SOLICITUD')
                ->where('id_solicitud', $solicitud->id_solicitud)
                ->update([
                    'estado' => 'En_Presupuesto',
                    'updated_at' => now()
                ]);
            
            DB::commit();
            return redirect()->route('solicitudes.show', $solicitud->id_solicitud)
                ->with('success', 'Solicitud enviada a Presupuesto exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Cancelar/Anular solicitud
     */
    public function cancelar(Request $request, $id)
    {
        $solicitud = Solicitud::findOrFail($id);
        
        // Solo puede cancelar el creador
        if ($solicitud->id_usuario_creador != Auth::user()->id_usuario) {
            abort(403, 'No tiene permisos para cancelar esta solicitud.');
        }

        // No se puede cancelar si ya está completada
        if (in_array($solicitud->estado, ['Completada', 'Cancelada'])) {
            return back()->with('error', 'No se puede cancelar una solicitud en este estado.');
        }

        $request->validate([
            'motivo' => 'required|string|min:10|max:4000'
        ]);

        DB::beginTransaction();
        try {
            DB::table('SOLICITUD')
                ->where('id_solicitud', $solicitud->id_solicitud)
                ->update([
                    'estado' => 'Cancelada',
                    'updated_at' => now()
                ]);

            DB::commit();
            return redirect()->route('solicitudes.index')
                ->with('success', 'Solicitud cancelada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cancelar: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar historial de cambios de estado
     */
    public function historial($id)
    {
        $solicitud = Solicitud::with([
            'historialEstados' => function($query) {
                $query->with('usuario')->orderBy('fecha_cambio', 'desc');
            }
        ])->findOrFail($id);

        return view('solicitudes.historial', compact('solicitud'));
    }

    /**
     * Método para cambiar estado (para roles especiales)
     */
    public function cambiarEstado(Request $request, $id)
    {
        $user = Auth::user();
        
        // Solo ciertos roles pueden cambiar estados
        if (!in_array($user->rol, ['Presupuesto', 'Compras', 'Autoridad', 'Admin'])) {
            abort(403, 'No tiene permisos para cambiar estados.');
        }

        $validated = $request->validate([
            'nuevo_estado' => 'required|string',
            'observaciones' => 'nullable|string|max:4000'
        ]);

        $solicitud = Solicitud::findOrFail($id);

        DB::beginTransaction();
        try {
            DB::table('SOLICITUD')
                ->where('id_solicitud', $solicitud->id_solicitud)
                ->update([
                    'estado' => $validated['nuevo_estado'],
                    'updated_at' => now()
                ]);

            DB::commit();
            return back()->with('success', 'Estado actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }
}