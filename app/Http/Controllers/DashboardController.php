<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Models\Proveedor;
use App\Models\Auditoria;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        switch ($user->rol) {
            case 'Solicitante':
                return $this->dashboardSolicitante($user);
            case 'Presupuesto':
                return $this->dashboardPresupuesto($user);
            case 'Compras':
                return $this->dashboardCompras($user);
            case 'Autoridad':
                return $this->dashboardAprobador($user);
            case 'Administrador':
                return $this->dashboardAdmin($user);
            default:
                return redirect()->route('login')->with('error', 'Rol no válido');
        }
    }

    private function dashboardSolicitante($user)
    {
        $solicitudesPendientes = Solicitud::where('id_usuario_creador', $user->id_usuario)
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $estadisticas = [
            'total' => Solicitud::where('id_usuario_creador', $user->id_usuario)->count(),
            'en_proceso' => Solicitud::where('id_usuario_creador', $user->id_usuario)
                ->whereIn('estado', ['pendiente', 'en_proceso'])
                ->count(),
            'aprobadas' => Solicitud::where('id_usuario_creador', $user->id_usuario)
                ->where('estado', 'aprobada')
                ->count(),
            'rechazadas' => Solicitud::where('id_usuario_creador', $user->id_usuario)
                ->where('estado', 'rechazada')
                ->count()
        ];

        return view('dashboard.solicitante', compact('solicitudesPendientes', 'estadisticas'));
    }

    private function dashboardPresupuesto($user)
    {
        $solicitudesPendientes = Solicitud::whereNull('validacion_presupuesto')
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $estadisticas = [
            'presupuesto_total' => DB::table('presupuestos')->sum('monto_total'),
            'monto_ejecutado' => DB::table('presupuestos')->where('estado', 'ejecutado')->sum('monto'),
            'pendientes' => Solicitud::whereNull('validacion_presupuesto')->count(),
            'validadas_hoy' => Solicitud::whereNotNull('validacion_presupuesto')
                ->whereDate('fecha_validacion_presupuesto', Carbon::today())
                ->count()
        ];

        $estadisticas['porcentaje_ejecutado'] = $estadisticas['presupuesto_total'] > 0 
            ? ($estadisticas['monto_ejecutado'] / $estadisticas['presupuesto_total']) * 100 
            : 0;

        return view('dashboard.presupuesto', compact('solicitudesPendientes', 'estadisticas'));
    }

    private function dashboardCompras($user)
    {
        $solicitudesPendientes = Solicitud::whereNotNull('validacion_presupuesto')
            ->whereNull('cotizacion_seleccionada')
            ->where('estado', 'en_proceso')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $estadisticas = [
            'por_cotizar' => Solicitud::whereNull('cotizacion_seleccionada')
                ->where('estado', 'en_proceso')
                ->count(),
            'en_proceso' => Solicitud::whereNotNull('cotizacion_seleccionada')
                ->where('estado', 'en_proceso')
                ->count(),
            'finalizadas' => Solicitud::where('estado', 'finalizada')->count(),
            'proveedores_activos' => Proveedor::where('activo', true)->count()
        ];

        $categorias = DB::table('categoria_productos')
            ->select('nombre', DB::raw('count(*) as total'))
            ->join('catalogo_productos', 'categoria_productos.id', '=', 'catalogo_productos.id_categoria')
            ->groupBy('nombre')
            ->get();

        return view('dashboard.compras', compact('solicitudesPendientes', 'estadisticas', 'categorias'));
    }

    private function dashboardAprobador($user)
    {
        $solicitudesPendientes = Solicitud::whereNotNull('cotizacion_seleccionada')
            ->whereNull('aprobacion_final')
            ->where('estado', 'en_proceso')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $estadisticas = [
            'pendientes' => Solicitud::whereNull('aprobacion_final')
                ->where('estado', 'en_proceso')
                ->count(),
            'aprobadas_hoy' => Solicitud::whereNotNull('aprobacion_final')
                ->whereDate('fecha_aprobacion', Carbon::today())
                ->count(),
            'total_aprobadas' => Solicitud::where('estado', 'aprobada')->count(),
            'total_rechazadas' => Solicitud::where('estado', 'rechazada')->count(),
            'total_pendientes' => Solicitud::where('estado', 'pendiente')->count()
        ];

        $estadisticas['tasa_aprobacion'] = ($estadisticas['total_aprobadas'] + $estadisticas['total_rechazadas']) > 0
            ? ($estadisticas['total_aprobadas'] / ($estadisticas['total_aprobadas'] + $estadisticas['total_rechazadas'])) * 100
            : 0;

        return view('dashboard.aprobador', compact('solicitudesPendientes', 'estadisticas'));
    }

    private function dashboardAdmin($user)
    {
        $estadisticas = [
            'usuarios_activos' => Usuario::where('activo', true)->count(),
            'total_usuarios' => Usuario::count(),
            'solicitudes_mes' => Solicitud::whereMonth('created_at', Carbon::now()->month)->count(),
            'presupuesto_ejecutado' => DB::table('presupuestos')->where('estado', 'ejecutado')->sum('monto'),
            'proveedores_activos' => Proveedor::where('activo', true)->count(),
            'total_proveedores' => Proveedor::count()
        ];

        // Calcular variación de solicitudes respecto al mes anterior
        $solicitudesMesAnterior = Solicitud::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        $estadisticas['variacion_solicitudes'] = $solicitudesMesAnterior > 0
            ? (($estadisticas['solicitudes_mes'] - $solicitudesMesAnterior) / $solicitudesMesAnterior) * 100
            : 100;

        // Calcular porcentaje de presupuesto ejecutado
        $presupuestoTotal = DB::table('presupuestos')->sum('monto_total');
        $estadisticas['porcentaje_presupuesto'] = $presupuestoTotal > 0
            ? ($estadisticas['presupuesto_ejecutado'] / $presupuestoTotal) * 100
            : 0;

        // Obtener actividad reciente
        $actividades = Auditoria::with('usuario')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Datos para el gráfico de rendimiento
        $rendimiento = [
            'labels' => [],
            'solicitudes' => [],
            'aprobaciones' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $rendimiento['labels'][] = $fecha->format('d/m');
            $rendimiento['solicitudes'][] = Solicitud::whereDate('created_at', $fecha)->count();
            $rendimiento['aprobaciones'][] = Solicitud::whereDate('fecha_aprobacion', $fecha)->count();
        }

        return view('dashboard.admin', compact('estadisticas', 'actividades', 'rendimiento'));
    }
}