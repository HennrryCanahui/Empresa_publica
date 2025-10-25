<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Presupuesto;
use App\Models\Aprobacion;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Estadísticas generales
        $totalSolicitudes = Solicitud::count();
        $solicitudesPendientes = Solicitud::where('estado', 'Pendiente')->count();
        $solicitudesAprobadas = Solicitud::where('estado', 'Aprobada')->count();
        $solicitudesRechazadas = Solicitud::where('estado', 'Rechazada')->count();

        // Montos
        $montoTotalSolicitudes = Solicitud::sum('monto_total_estimado');
        $montoAprobado = Aprobacion::where('decision', 'Aprobada')->sum('monto_aprobado');

        // Proveedores
        $totalProveedores = Proveedor::where('activo', true)->count();

        // Usuarios por rol
        $usuariosPorRol = User::select('rol', DB::raw('count(*) as total'))
                             ->groupBy('rol')
                             ->get();

        return view('reportes.index', compact(
            'totalSolicitudes',
            'solicitudesPendientes',
            'solicitudesAprobadas',
            'solicitudesRechazadas',
            'montoTotalSolicitudes',
            'montoAprobado',
            'totalProveedores',
            'usuariosPorRol'
        ));
    }

    /**
     * Reporte de solicitudes
     */
    public function solicitudes(Request $request)
    {
        $query = Solicitud::with(['unidadSolicitante', 'usuarioCreador']);

        // Filtros
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_creacion', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_creacion', '<=', $request->fecha_hasta);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $solicitudes = $query->orderBy('fecha_creacion', 'desc')->get();

        return view('reportes.solicitudes', compact('solicitudes'));
    }

    /**
     * Reporte de proveedores
     */
    public function proveedores()
    {
        $proveedores = Proveedor::withCount('cotizaciones')
                                ->orderBy('razon_social')
                                ->get();

        return view('reportes.proveedores', compact('proveedores'));
    }

    /**
     * Reporte de presupuesto
     */
    public function presupuesto(Request $request)
    {
        $query = Presupuesto::with(['solicitud.unidadSolicitante', 'solicitud.usuarioCreador']);

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $presupuestos = $query->orderBy('created_at', 'desc')->get();

        return view('reportes.presupuesto', compact('presupuestos'));
    }

    /**
     * Exportar reportes
     */
    public function exportar(Request $request)
    {
        // Aquí se implementaría la exportación a Excel/PDF
        return back()->with('info', 'Funcionalidad de exportación en desarrollo.');
    }
}
