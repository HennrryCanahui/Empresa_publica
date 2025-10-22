<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\Solicitud;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CotizacionController extends Controller
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
        $solicitudes = Solicitud::whereIn('estado', ['Presupuestada', 'En_Cotizacion'])
            ->orderBy('fecha_creacion', 'asc')
            ->paginate(10);

        return view('cotizaciones.index', compact('solicitudes'));
    }

    public function create(Solicitud $solicitud)
    {
        if (!in_array($solicitud->estado, ['Presupuestada', 'En_Cotizacion'])) {
            return redirect()->route('cotizaciones.index')
                ->with('error', 'La solicitud no está en estado para cotizar.');
        }

        $proveedores = Proveedor::where('activo', 1)->get();
        return view('cotizaciones.create', compact('solicitud', 'proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_solicitud' => 'required|exists:solicitud,id_solicitud',
            'id_proveedor' => 'required|exists:proveedor,id_proveedor',
            'fecha_cotizacion' => 'required|date',
            'fecha_validez' => 'required|date|after:fecha_cotizacion',
            'tiempo_entrega_dias' => 'required|integer|min:1',
            'condiciones_pago' => 'required|string|max:4000',
            'documento' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:catalogo_producto,id_producto',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'productos.*.observaciones' => 'nullable|string|max:4000'
        ]);

        DB::beginTransaction();
        try {
            $solicitud = Solicitud::findOrFail($request->id_solicitud);
            
            // Crear la cotización
            $cotizacion = new Cotizacion([
                'id_solicitud' => $request->id_solicitud,
                'id_proveedor' => $request->id_proveedor,
                'fecha_cotizacion' => $request->fecha_cotizacion,
                'fecha_validez' => $request->fecha_validez,
                'tiempo_entrega_dias' => $request->tiempo_entrega_dias,
                'condiciones_pago' => $request->condiciones_pago,
                'estado' => 'Activa',
                'id_usuario_compras' => Auth::id()
            ]);

            // Generar número de cotización
            $contador = Cotizacion::whereYear('fecha_cotizacion', now()->year)->count() + 1;
            $cotizacion->numero_cotizacion = 'COT-' . now()->year . '-' . str_pad($contador, 5, '0', STR_PAD_LEFT);
            
            // Subir documento si existe
            if ($request->hasFile('documento')) {
                $path = $request->file('documento')->store('cotizaciones', 'public');
                $cotizacion->documento_cotizacion = $path;
            }

            $cotizacion->save();

            // Crear detalles
            $montoTotal = 0;
            foreach ($request->productos as $producto) {
                $detalle = new DetalleCotizacion([
                    'id_cotizacion' => $cotizacion->id_cotizacion,
                    'id_producto' => $producto['id_producto'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio_unitario'],
                    'observaciones' => $producto['observaciones'] ?? null
                ]);
                $detalle->save();
                $montoTotal += $detalle->precio_total;
            }

            // Actualizar monto total
            $cotizacion->monto_total = $montoTotal;
            $cotizacion->save();

            // Actualizar estado de la solicitud si es la primera cotización
            if ($solicitud->estado === 'Presupuestada') {
                $solicitud->cambiarEstado('En_Cotizacion', Auth::id());
            }

            DB::commit();
            return redirect()->route('cotizaciones.comparar', $solicitud)
                ->with('success', 'Cotización registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            return back()->with('error', 'Error al registrar la cotización: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function comparar(Solicitud $solicitud)
    {
        if (!in_array($solicitud->estado, ['En_Cotizacion', 'Cotizada'])) {
            return redirect()->route('cotizaciones.index')
                ->with('error', 'La solicitud no está en estado para comparar cotizaciones.');
        }

        $cotizaciones = $solicitud->cotizaciones()
            ->with(['proveedor', 'detalles.producto'])
            ->get();

        return view('cotizaciones.comparar', compact('solicitud', 'cotizaciones'));
    }

    public function seleccionar(Request $request, Cotizacion $cotizacion)
    {
        if ($cotizacion->estado !== 'Activa') {
            return back()->with('error', 'La cotización no está en estado para ser seleccionada.');
        }

        DB::beginTransaction();
        try {
            // Marcar la cotización como seleccionada
            $cotizacion->update(['estado' => 'Seleccionada']);
            
            // Marcar las demás cotizaciones como descartadas
            Cotizacion::where('id_solicitud', $cotizacion->id_solicitud)
                ->where('id_cotizacion', '!=', $cotizacion->id_cotizacion)
                ->update(['estado' => 'Descartada']);

            // Actualizar estado de la solicitud
            $solicitud = $cotizacion->solicitud;
            $solicitud->cambiarEstado('Cotizada', Auth::id(), 'Cotización seleccionada');

            DB::commit();
            return redirect()->route('cotizaciones.index')
                ->with('success', 'Cotización seleccionada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al seleccionar la cotización: ' . $e->getMessage());
        }
    }
}