@extends('layouts.app')

@section('title', 'Dashboard Compras')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard de Compras</h1>
    
    <div class="row">
        <!-- Solicitudes para Cotizar -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Solicitudes para Cotizar</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Unidad</th>
                                    <th>Productos</th>
                                    <th>Estado</th>
                                    <th>Fecha Límite</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudesPendientes as $solicitud)
                                <tr>
                                    <td>{{ $solicitud->id }}</td>
                                    <td>{{ $solicitud->unidad->nombre }}</td>
                                    <td>{{ $solicitud->items_count }}</td>
                                    <td>
                                        <span class="badge bg-{{ $solicitud->estado_color }}">
                                            {{ $solicitud->estado }}
                                        </span>
                                    </td>
                                    <td>{{ $solicitud->fecha_limite->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('cotizaciones.create', $solicitud->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            Cotizar
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

        <!-- Estadísticas de Compras -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Estadísticas de Compras</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <h6>Por Cotizar</h6>
                                <h4>{{ $estadisticas['por_cotizar'] }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <h6>En Proceso</h6>
                                <h4>{{ $estadisticas['en_proceso'] }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <h6>Finalizadas</h6>
                                <h4>{{ $estadisticas['finalizadas'] }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <h6>Proveedores</h6>
                                <h4>{{ $estadisticas['proveedores_activos'] }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6>Compras por Categoría</h6>
                        <canvas id="comprasPorCategoria"></canvas>
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
                        <a href="{{ route('cotizaciones.index') }}" class="btn btn-primary">
                            <i class="bi bi-list-check"></i> Ver Todas las Cotizaciones
                        </a>
                        <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
                            <i class="bi bi-building"></i> Gestionar Proveedores
                        </a>
                        <a href="{{ route('reportes.proveedores') }}" class="btn btn-info">
                            <i class="bi bi-graph-up"></i> Reportes de Proveedores
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
    const ctx = document.getElementById('comprasPorCategoria').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categorias->pluck('nombre')) !!},
            datasets: [{
                data: {!! json_encode($categorias->pluck('total')) !!},
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                ]
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