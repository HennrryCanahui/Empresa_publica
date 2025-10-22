@extends('layouts.app')

@section('title', 'Dashboard Aprobador')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard de Aprobador</h1>
    
    <div class="row">
        <!-- Solicitudes por Aprobar -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Solicitudes Pendientes de Aprobación</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Unidad</th>
                                    <th>Solicitante</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudesPendientes as $solicitud)
                                <tr>
                                    <td>{{ $solicitud->id }}</td>
                                    <td>{{ $solicitud->unidad->nombre }}</td>
                                    <td>{{ $solicitud->usuario->nombre }}</td>
                                    <td>${{ number_format($solicitud->monto_total, 2) }}</td>
                                    <td>{{ $solicitud->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('aprobaciones.evaluar', $solicitud->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            Evaluar
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Aprobación -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Estadísticas de Aprobación</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <h6>Pendientes</h6>
                                <h4>{{ $estadisticas['pendientes'] }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <h6>Aprobadas Hoy</h6>
                                <h4>{{ $estadisticas['aprobadas_hoy'] }}</h4>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mt-4">
                                <h6>Tasa de Aprobación</h6>
                                <div class="progress">
                                    <div class="progress-bar bg-success" 
                                         style="width: {{ $estadisticas['tasa_aprobacion'] }}%">
                                        {{ $estadisticas['tasa_aprobacion'] }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6>Solicitudes por Estado</h6>
                        <canvas id="solicitudesPorEstado"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('aprobaciones.index') }}" class="btn btn-primary">
                            <i class="bi bi-list-check"></i> Ver Todas las Aprobaciones
                        </a>
                        <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
                            <i class="bi bi-graph-up"></i> Ver Reportes
                        </a>
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
    const ctx = document.getElementById('solicitudesPorEstado').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Aprobadas', 'Rechazadas', 'Pendientes'],
            datasets: [{
                data: [
                    {{ $estadisticas['total_aprobadas'] }},
                    {{ $estadisticas['total_rechazadas'] }},
                    {{ $estadisticas['total_pendientes'] }}
                ],
                backgroundColor: ['#1cc88a', '#e74a3b', '#f6c23e']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
@endpush
@endsection