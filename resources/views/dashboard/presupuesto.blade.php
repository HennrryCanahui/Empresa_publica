@extends('layouts.app')

@section('title', 'Dashboard Presupuesto')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard de Presupuesto</h1>
    
    <div class="row">
        <!-- Solicitudes por Validar -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Solicitudes por Validar Presupuesto</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Solicitante</th>
                                    <th>Unidad</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudesPendientes as $solicitud)
                                <tr>
                                    <td>{{ $solicitud->id }}</td>
                                    <td>{{ $solicitud->usuario->nombre }}</td>
                                    <td>{{ $solicitud->unidad->nombre }}</td>
                                    <td>{{ number_format($solicitud->monto_total, 2) }}</td>
                                    <td>{{ $solicitud->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('presupuestos.validar', $solicitud->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            Validar
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

        <!-- Estadísticas de Presupuesto -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Estadísticas de Presupuesto</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Presupuesto Total</h6>
                        <h3>${{ number_format($estadisticas['presupuesto_total'], 2) }}</h3>
                        <div class="progress">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ $estadisticas['porcentaje_ejecutado'] }}%">
                                {{ $estadisticas['porcentaje_ejecutado'] }}%
                            </div>
                        </div>
                        <small class="text-muted">Ejecutado: ${{ number_format($estadisticas['monto_ejecutado'], 2) }}</small>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <h6>Pendientes</h6>
                                <h4>{{ $estadisticas['pendientes'] }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <h6>Validadas Hoy</h6>
                                <h4>{{ $estadisticas['validadas_hoy'] }}</h4>
                            </div>
                        </div>
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
                        <a href="{{ route('presupuestos.index') }}" class="btn btn-primary">
                            <i class="bi bi-list-check"></i> Ver Todas las Validaciones
                        </a>
                        <a href="{{ route('reportes.presupuesto') }}" class="btn btn-secondary">
                            <i class="bi bi-graph-up"></i> Reportes de Presupuesto
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection