@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <h2>Bienvenido, {{ Auth::user()->nombre }}</h2>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-file-text"></i> Total Solicitudes
                </h5>
                <h2 class="mb-0">{{ $total_solicitudes }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-clock"></i> En Proceso
                </h5>
                <h2 class="mb-0">{{ $solicitudes_proceso }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-check-circle"></i> Completadas
                </h5>
                <h2 class="mb-0">{{ $solicitudes_completadas }}</h2>
            </div>
        </div>
    </div>
</div>

@role('Solicitante')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Mis Últimas Solicitudes</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @forelse($mis_solicitudes ?? [] as $solicitud)
                    <a href="{{ route('solicitudes.show', $solicitud) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $solicitud->numero_solicitud }}</h6>
                            <small class="text-muted">{{ $solicitud->created_at->format('d/m/Y') }}</small>
                        </div>
                        <p class="mb-1">{{ Str::limit($solicitud->descripcion, 100) }}</p>
                        <small class="text-muted">Estado: {{ $solicitud->estado }}</small>
                    </a>
                    @empty
                    <p class="text-muted mb-0">No hay solicitudes recientes.</p>
                    @endforelse
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('solicitudes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nueva Solicitud
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Estado de Mis Solicitudes</h5>
            </div>
            <div class="card-body">
                <canvas id="solicitudesChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endrole

@role('Presupuesto')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Solicitudes Pendientes de Validación</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Unidad</th>
                                <th>Fecha</th>
                                <th>Monto Est.</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($solicitudes_pendientes ?? [] as $solicitud)
                            <tr>
                                <td>{{ $solicitud->numero_solicitud }}</td>
                                <td>{{ $solicitud->unidadSolicitante->nombre }}</td>
                                <td>{{ $solicitud->created_at->format('d/m/Y') }}</td>
                                <td>{{ number_format($solicitud->monto_estimado_total, 2) }}</td>
                                <td>
                                    <a href="{{ route('presupuestos.validar', $solicitud) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-check-circle"></i> Validar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay solicitudes pendientes</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endrole

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@role('Solicitante')
// Configuración del gráfico de solicitudes
const ctx = document.getElementById('solicitudesChart');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['En Proceso', 'Aprobadas', 'Rechazadas', 'Completadas'],
        datasets: [{
            data: [
                {{ $stats['proceso'] ?? 0 }},
                {{ $stats['aprobadas'] ?? 0 }},
                {{ $stats['rechazadas'] ?? 0 }},
                {{ $stats['completadas'] ?? 0 }}
            ],
            backgroundColor: [
                '#ffc107',
                '#0d6efd',
                '#dc3545',
                '#198754'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
@endrole
</script>
@endpush
@endsection  