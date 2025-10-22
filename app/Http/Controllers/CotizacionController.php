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
            'fecha_validez' => 'nullable|date|after:fecha_cotizacion',
            'tiempo_entrega' => 'required|integer|min:1',
            'condiciones_pago' => 'required|string|max:4000',
            'archivo_cotizacion' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'productos' => 'required|array|min:1',
            'productos.*' => 'required|exists:catalogo_producto,id_producto',
            'cantidades' => 'required|array',
            'cantidades.*' => 'required|numeric|min:0.01',
            'precios' => 'required|array',
            'precios.*' => 'required|numeric|min:0',
            'observaciones' => 'nullable|array',
            'observaciones.*' => 'nullable|string|max:4000'
        ]);

        DB::beginTransaction();
        try {
            $solicitud = Solicitud::findOrFail($request->id_solicitud);
            
            // Crear la cotización
            $cotizacion = new Cotizacion([
                'id_solicitud' => $request->id_solicitud,
                'id_proveedor' => $request->id_proveedor,
                'fecha_cotizacion' => $request->fecha_cotizacion,
                'fecha_validez' => $request->fecha_validez ?? null,
                'tiempo_entrega_dias' => $request->tiempo_entrega,
                'condiciones_pago' => $request->condiciones_pago,
                'estado' => 'Activa',
                'id_usuario_compras' => Auth::id()
            ]);

            // Generar número de cotización sólo si no fue provisto
            if ($request->filled('numero_cotizacion')) {
                $cotizacion->numero_cotizacion = $request->numero_cotizacion;
            } else {
                $contador = Cotizacion::whereYear('fecha_cotizacion', now()->year)->count() + 1;
                $cotizacion->numero_cotizacion = 'COT-' . now()->year . '-' . str_pad($contador, 5, '0', STR_PAD_LEFT);
            }

            // Subir documento si existe (archivo_cotizacion en la vista)
            if ($request->hasFile('archivo_cotizacion')) {
                $path = $request->file('archivo_cotizacion')->store('cotizaciones', 'public');
                $cotizacion->documento_cotizacion = $path;
            }

            $cotizacion->save();

            // Crear detalles a partir de arrays planos productos[], cantidades[], precios[], observaciones[]
            $productos = $request->input('productos', []);
            $cantidades = $request->input('cantidades', []);
            $precios = $request->input('precios', []);
            $observaciones = $request->input('observaciones', []);

            $montoTotal = 0;
            for ($i = 0; $i < count($productos); $i++) {
                $idProducto = $productos[$i];
                $cantidad = $cantidades[$i] ?? 0;
                $precioUnitario = $precios[$i] ?? 0;
                $obs = $observaciones[$i] ?? null;

                $detalle = new DetalleCotizacion([
                    'id_cotizacion' => $cotizacion->id_cotizacion,
                    'id_producto' => $idProducto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'observaciones' => $obs
                ]);
                $detalle->save();
                $montoTotal += (float) $detalle->precio_total;
            }

            // Actualizar monto total
            $cotizacion->monto_total = $montoTotal;
            $cotizacion->save();

            // Actualizar estado de la solicitud si corresponde
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

    public function seleccionar(Request $request)
    {
        // Nueva versión: recibir id_cotizacion desde el request
        $request->validate([
            'id_cotizacion' => 'required|exists:cotizacion,id_cotizacion',
            'justificacion' => 'nullable|string|max:4000'
        ]);

        DB::beginTransaction();
        try {
            $id = $request->input('id_cotizacion');
            $cotizacion = Cotizacion::findOrFail($id);

            if ($cotizacion->estado !== 'Activa') {
                return back()->with('error', 'La cotización no está en estado para ser seleccionada.');
            }

            // Marcar la cotización como seleccionada
            $cotizacion->update(['estado' => 'Seleccionada']);

            // Marcar las demás cotizaciones como descartadas
            Cotizacion::where('id_solicitud', $cotizacion->id_solicitud)
                ->where('id_cotizacion', '!=', $cotizacion->id_cotizacion)
                ->update(['estado' => 'Descartada']);

            // Actualizar estado de la solicitud: pasar a etapa de aprobación
            $solicitud = $cotizacion->solicitud;
            $solicitud->cambiarEstado('En_Aprobacion', Auth::id(), $request->input('justificacion'));

            DB::commit();
            return redirect()->route('cotizaciones.index')
                ->with('success', 'Cotización seleccionada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al seleccionar la cotización: ' . $e->getMessage());
        }
    }
}