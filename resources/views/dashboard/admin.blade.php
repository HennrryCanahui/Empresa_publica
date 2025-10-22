@extends('layouts.app')

@section('title', 'Dashboard Administrador')

@section('content')
<div class="container">
    <h1 class="mb-4">Panel de Administración</h1>
    
    <div class="row">
        <!-- Estadísticas Generales -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Estadísticas Generales</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6>Usuarios Activos</h6>
                                <h3>{{ $estadisticas['usuarios_activos'] }}</h3>
                                <small class="text-muted">de {{ $estadisticas['total_usuarios'] }} usuarios</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6>Solicitudes Este Mes</h6>
                                <h3>{{ $estadisticas['solicitudes_mes'] }}</h3>
                                <small class="text-muted">{{ $estadisticas['variacion_solicitudes'] }}% vs mes anterior</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6>Presupuesto Ejecutado</h6>
                                <h3>${{ number_format($estadisticas['presupuesto_ejecutado'], 2) }}</h3>
                                <small class="text-muted">{{ $estadisticas['porcentaje_presupuesto'] }}% del total</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6>Proveedores Activos</h6>
                                <h3>{{ $estadisticas['proveedores_activos'] }}</h3>
                                <small class="text-muted">de {{ $estadisticas['total_proveedores'] }} registrados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actividad Reciente</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($actividades as $actividad)
                                <tr>
                                    <td>{{ $actividad->usuario->nombre }}</td>
                                    <td>{{ $actividad->descripcion }}</td>
                                    <td>{{ $actividad->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Rendimiento -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Rendimiento del Sistema</h5>
                </div>
                <div class="card-body">
                    <canvas id="rendimientoChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Administrativas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Acciones Administrativas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-primary w-100">
                                <i class="bi bi-people"></i> Gestionar Usuarios
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('proveedores.index') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-building"></i> Gestionar Proveedores
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('reportes.index') }}" class="btn btn-info w-100">
                                <i class="bi bi-graph-up"></i> Ver Reportes
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('auditoria.index') }}" class="btn btn-warning w-100">
                                <i class="bi bi-shield-check"></i> Registros de Auditoría
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('rendimientoChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($rendimiento['labels']) !!},
            datasets: [{
                label: 'Solicitudes',
                data: {!! json_encode($rendimiento['solicitudes']) !!},
                borderColor: '#4e73df',
                tension: 0.1
            }, {
                label: 'Aprobaciones',
                data: {!! json_encode($rendimiento['aprobaciones']) !!},
                borderColor: '#1cc88a',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
@endsection