<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\DetalleSolicitud;
use App\Models\CatalogoProducto;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $query = Solicitud::query();
        
        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_creacion', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_creacion', '<=', $request->fecha_hasta);
        }

        // Si no es admin, mostrar solo las solicitudes relacionadas
        if (Auth::user()->rol !== 'Admin') {
            $query->where(function($q) {
                $q->where('id_usuario_creador', Auth::id())
                  ->orWhere('id_unidad_solicitante', Auth::user()->id_unidad);
            });
        }

        $solicitudes = $query->orderBy('fecha_creacion', 'desc')
                           ->paginate(10);

        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $unidades = Unidad::where('activo', 1)->get();
        $productos = CatalogoProducto::where('activo', 1)->get();
        return view('solicitudes.create', compact('unidades', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:4000',
            'justificacion' => 'required|string|max:4000',
            'prioridad' => 'required|in:Baja,Media,Alta,Urgente',
            'fecha_limite' => 'nullable|date|after:today',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:catalogo_producto,id_producto',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.especificaciones' => 'nullable|string|max:4000'
        ]);

        DB::beginTransaction();
        try {
            // Crear la solicitud
            $solicitud = new Solicitud([
                'descripcion' => $request->descripcion,
                'justificacion' => $request->justificacion,
                'estado' => 'Creada',
                'id_unidad_solicitante' => Auth::user()->id_unidad,
                'id_usuario_creador' => Auth::id(),
                'prioridad' => $request->prioridad,
                'fecha_limite' => $request->fecha_limite
            ]);

            // Generar número de solicitud
            $contador = Solicitud::whereYear('fecha_creacion', now()->year)->count() + 1;
            $solicitud->numero_solicitud = 'SOL-' . now()->year . '-' . str_pad($contador, 5, '0', STR_PAD_LEFT);
            $solicitud->save();

            // Crear detalles
            foreach ($request->productos as $producto) {
                $detalle = new DetalleSolicitud([
                    'id_solicitud' => $solicitud->id_solicitud,
                    'id_producto' => $producto['id_producto'],
                    'cantidad' => $producto['cantidad'],
                    'especificaciones_adicionales' => $producto['especificaciones'] ?? null
                ]);
                
                // Obtener precio referencia del catálogo
                $catalogoProducto = CatalogoProducto::find($producto['id_producto']);
                if ($catalogoProducto->precio_referencia) {
                    $detalle->precio_estimado_unitario = $catalogoProducto->precio_referencia;
                    $detalle->precio_estimado_total = $producto['cantidad'] * $catalogoProducto->precio_referencia;
                }
                
                $detalle->save();
            }

            DB::commit();
            return redirect()->route('solicitudes.show', $solicitud)
                           ->with('success', 'Solicitud creada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al crear la solicitud: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show(Solicitud $solicitud)
    {
        $this->authorize('view', $solicitud);
        
        $solicitud->load(['detalles.producto', 'unidadSolicitante', 'usuarioCreador', 
                         'presupuesto', 'cotizaciones', 'aprobacion', 'historialEstados']);
        
        return view('solicitudes.show', compact('solicitud'));
    }

    public function edit(Solicitud $solicitud)
    {
        $this->authorize('update', $solicitud);
        
        if (!in_array($solicitud->estado, ['Creada', 'En_Presupuesto'])) {
            return back()->with('error', 'La solicitud no puede ser editada en su estado actual.');
        }

        $unidades = Unidad::where('activo', 1)->get();
        $productos = CatalogoProducto::where('activo', 1)->get();
        
        return view('solicitudes.edit', compact('solicitud', 'unidades', 'productos'));
    }

    public function update(Request $request, Solicitud $solicitud)
    {
        $this->authorize('update', $solicitud);
        
        if (!in_array($solicitud->estado, ['Creada', 'En_Presupuesto'])) {
            return back()->with('error', 'La solicitud no puede ser editada en su estado actual.');
        }

        $request->validate([
            'descripcion' => 'required|string|max:4000',
            'justificacion' => 'required|string|max:4000',
            'prioridad' => 'required|in:Baja,Media,Alta,Urgente',
            'fecha_limite' => 'nullable|date|after:today',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:catalogo_producto,id_producto',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.especificaciones' => 'nullable|string|max:4000'
        ]);

        DB::beginTransaction();
        try {
            // Actualizar solicitud
            $solicitud->update([
                'descripcion' => $request->descripcion,
                'justificacion' => $request->justificacion,
                'prioridad' => $request->prioridad,
                'fecha_limite' => $request->fecha_limite
            ]);

            // Eliminar detalles existentes
            $solicitud->detalles()->delete();

            // Crear nuevos detalles
            foreach ($request->productos as $producto) {
                $detalle = new DetalleSolicitud([
                    'id_solicitud' => $solicitud->id_solicitud,
                    'id_producto' => $producto['id_producto'],
                    'cantidad' => $producto['cantidad'],
                    'especificaciones_adicionales' => $producto['especificaciones'] ?? null
                ]);
                
                // Obtener precio referencia del catálogo
                $catalogoProducto = CatalogoProducto::find($producto['id_producto']);
                if ($catalogoProducto->precio_referencia) {
                    $detalle->precio_estimado_unitario = $catalogoProducto->precio_referencia;
                    $detalle->precio_estimado_total = $producto['cantidad'] * $catalogoProducto->precio_referencia;
                }
                
                $detalle->save();
            }

            DB::commit();
            return redirect()->route('solicitudes.show', $solicitud)
                           ->with('success', 'Solicitud actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al actualizar la solicitud: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function destroy(Solicitud $solicitud)
    {
        $this->authorize('delete', $solicitud);
        
        if ($solicitud->estado !== 'Creada') {
            return back()->with('error', 'Solo se pueden eliminar solicitudes en estado Creada.');
        }

        try {
            $solicitud->delete();
            return redirect()->route('solicitudes.index')
                           ->with('success', 'Solicitud eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la solicitud: ' . $e->getMessage());
        }
    }
}