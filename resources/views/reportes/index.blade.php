@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Reportes y Estadísticas</h2>
@endsection

@section('content')
<div class="mb-4">
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Panel de Reportes</strong> - Visualice estadísticas y métricas del sistema de compras.
    </div>
</div>

<!-- Estadísticas Generales -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-file-text fs-1 text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Solicitudes</h6>
                        <h3 class="mb-0">{{ $totalSolicitudes }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-hourglass-split fs-1 text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Pendientes</h6>
                        <h3 class="mb-0">{{ $solicitudesPendientes }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle fs-1 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Aprobadas</h6>
                        <h3 class="mb-0">{{ $solicitudesAprobadas }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-x-circle fs-1 text-danger"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Rechazadas</h6>
                        <h3 class="mb-0">{{ $solicitudesRechazadas }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Montos -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-currency-dollar fs-1 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Monto Total Solicitado</h6>
                        <h3 class="mb-0 text-info">Q {{ number_format($montoTotalSolicitudes ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-cash-stack fs-1 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Monto Total Aprobado</h6>
                        <h3 class="mb-0 text-success">Q {{ number_format($montoAprobado ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reportes Disponibles -->
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-file-earmark-text display-4 text-primary mb-3"></i>
                <h5 class="card-title">Reporte de Solicitudes</h5>
                <p class="card-text text-muted">Detalle completo de todas las solicitudes por fecha y estado</p>
                <a href="{{ route('admin.reportes.solicitudes') }}" class="btn btn-primary">
                    <i class="bi bi-eye me-1"></i>Ver Reporte
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-building display-4 text-success mb-3"></i>
                <h5 class="card-title">Reporte de Proveedores</h5>
                <p class="card-text text-muted">Listado de proveedores y sus cotizaciones realizadas</p>
                <a href="{{ route('admin.reportes.proveedores') }}" class="btn btn-success">
                    <i class="bi bi-eye me-1"></i>Ver Reporte
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-calculator display-4 text-warning mb-3"></i>
                <h5 class="card-title">Reporte de Presupuesto</h5>
                <p class="card-text text-muted">Análisis de validaciones presupuestarias realizadas</p>
                <a href="{{ route('admin.reportes.presupuesto') }}" class="btn btn-warning">
                    <i class="bi bi-eye me-1"></i>Ver Reporte
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Usuarios por Rol -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Usuarios por Rol</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Rol</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Porcentaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalUsuarios = $usuariosPorRol->sum('total'); @endphp
                            @foreach($usuariosPorRol as $rol)
                                <tr>
                                    <td>
                                        @if($rol->rol == 'Admin')
                                            <span class="badge bg-danger">{{ $rol->rol }}</span>
                                        @elseif($rol->rol == 'Autoridad')
                                            <span class="badge bg-success">{{ $rol->rol }}</span>
                                        @elseif($rol->rol == 'Presupuesto')
                                            <span class="badge bg-warning text-dark">{{ $rol->rol }}</span>
                                        @elseif($rol->rol == 'Compras')
                                            <span class="badge bg-info">{{ $rol->rol }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $rol->rol }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center"><strong>{{ $rol->total }}</strong></td>
                                    <td class="text-end">{{ $totalUsuarios > 0 ? number_format(($rol->total / $totalUsuarios) * 100, 1) : 0 }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td><strong>TOTAL</strong></td>
                                <td class="text-center"><strong>{{ $totalUsuarios }}</strong></td>
                                <td class="text-end"><strong>100%</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
