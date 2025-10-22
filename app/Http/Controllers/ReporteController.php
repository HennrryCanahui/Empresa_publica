<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Adquisicion;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        $unidades = Unidad::where('activo', true)->orderBy('nombre')->get();
        return view('reportes.index', compact('unidades'));
    }

    public function solicitudesPorEstado(Request $request)
    {
        $query = Solicitud::with(['unidadSolicitante', 'usuarioCreador'])
            ->select('solicitud.*')
            ->selectRaw('COUNT(*) as total');

        if ($request->has('fecha_desde')) {
            $query->whereDate('fecha_creacion', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('fecha_creacion', '<=', $request->fecha_hasta);
        }

        if ($request->has('unidad')) {
            $query->where('id_unidad_solicitante', $request->unidad);
        }

        $resultados = $query->groupBy('estado')->get();

        return response()->json($resultados);
    }

    public function gastosPorUnidad(Request $request)
    {
        $query = Adquisicion::join('solicitud', 'adquisicion.id_solicitud', '=', 'solicitud.id_solicitud')
            ->join('unidad', 'solicitud.id_unidad_solicitante', '=', 'unidad.id_unidad')
            ->select('unidad.nombre', DB::raw('SUM(adquisicion.monto_total) as total_gastado'))
            ->where('adquisicion.estado', 'Completada');

        if ($request->has('fecha_desde')) {
            $query->whereDate('adquisicion.fecha_orden', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('adquisicion.fecha_orden', '<=', $request->fecha_hasta);
        }

        $resultados = $query->groupBy('unidad.id_unidad', 'unidad.nombre')
            ->orderBy('total_gastado', 'desc')
            ->get();

        return response()->json($resultados);
    }

    public function tiempoPromedioSolicitudes(Request $request)
    {
        $query = Solicitud::selectRaw('
            AVG(TIMESTAMPDIFF(DAY, fecha_creacion, fecha_completado)) as promedio_dias,
            MONTH(fecha_creacion) as mes
        ')
        ->whereNotNull('fecha_completado')
        ->where('estado', 'Completada');

        if ($request->has('anio')) {
            $query->whereYear('fecha_creacion', $request->anio);
        }

        $resultados = $query->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return response()->json($resultados);
    }
}