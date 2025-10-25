@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-folder2-open me-2"></i>Mis Solicitudes</h2>
@endsection

@section('content')
<!-- Mensajes de alerta -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-folder2-open fs-1 text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Solicitudes</h6>
                        <h3 class="mb-0">{{ $solicitudes->total() }}</h3>
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
                        <h6 class="text-muted mb-1">En Proceso</h6>
                        <h3 class="mb-0">{{ $solicitudes->whereIn('estado', ['Creada', 'En_Presupuesto', 'Presupuestada', 'En_Cotizacion', 'Cotizada', 'En_Aprobacion'])->count() }}</h3>
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
                        <h3 class="mb-0">{{ $solicitudes->where('estado', 'Aprobada')->count() }}</h3>
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
                        <i class="bi bi-currency-dollar fs-1 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Monto Total</h6>
                        <h3 class="mb-0">Q {{ number_format($solicitudes->sum('monto_total_estimado'), 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botón crear nueva solicitud -->
<div class="mb-3">
    <a href="{{ route('solicitudes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Nueva Solicitud
    </a>
    <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-clock-history me-2"></i>Ver Mis Solicitudes
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('solicitudes.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="Creada" {{ request('estado') == 'Creada' ? 'selected' : '' }}>Creada</option>
                    <option value="En_Presupuesto" {{ request('estado') == 'En_Presupuesto' ? 'selected' : '' }}>En Presupuesto</option>
                    <option value="Presupuestada" {{ request('estado') == 'Presupuestada' ? 'selected' : '' }}>Presupuestada</option>
                    <option value="En_Cotizacion" {{ request('estado') == 'En_Cotizacion' ? 'selected' : '' }}>En Cotización</option>
                    <option value="Cotizada" {{ request('estado') == 'Cotizada' ? 'selected' : '' }}>Cotizada</option>
                    <option value="En_Aprobacion" {{ request('estado') == 'En_Aprobacion' ? 'selected' : '' }}>En Aprobación</option>
                    <option value="Aprobada" {{ request('estado') == 'Aprobada' ? 'selected' : '' }}>Aprobada</option>
                    <option value="En_Adquisicion" {{ request('estado') == 'En_Adquisicion' ? 'selected' : '' }}>En Adquisición</option>
                    <option value="Completada" {{ request('estado') == 'Completada' ? 'selected' : '' }}>Completada</option>
                    <option value="Rechazada" {{ request('estado') == 'Rechazada' ? 'selected' : '' }}>Rechazada</option>
                    <option value="Cancelada" {{ request('estado') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Prioridad</label>
                <select name="prioridad" class="form-select">
                    <option value="">Todas</option>
                    <option value="Urgente" {{ request('prioridad') == 'Urgente' ? 'selected' : '' }}>Urgente</option>
                    <option value="Alta" {{ request('prioridad') == 'Alta' ? 'selected' : '' }}>Alta</option>
                    <option value="Media" {{ request('prioridad') == 'Media' ? 'selected' : '' }}>Media</option>
                    <option value="Baja" {{ request('prioridad') == 'Baja' ? 'selected' : '' }}>Baja</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label small">Buscar</label>
                <input type="text" name="buscar" class="form-control" placeholder="Número o descripción..." value="{{ request('buscar') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de solicitudes -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-table me-2"></i>Listado de Solicitudes</h5>
    </div>
    <div class="card-body p-0">
        @if($solicitudes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Prioridad</th>
                            <th class="text-end">Monto Estimado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitudes as $solicitud)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $solicitud->numero_solicitud }}</strong>
                                </td>
                                <td>
                                    <small>{{ $solicitud->fecha_creacion->format('d/m/Y') }}</small><br>
                                    <small class="text-muted">{{ $solicitud->fecha_creacion->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $solicitud->descripcion }}">
                                        {{ $solicitud->descripcion }}
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-building me-1"></i>{{ $solicitud->unidadSolicitante->nombre_unidad ?? 'N/A' }}
                                    </small>
                                </td>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($solicitud->estado) {
                                            'Creada' => 'bg-secondary',
                                            'En_Presupuesto' => 'bg-info',
                                            'Presupuestada' => 'bg-primary',
                                            'En_Cotizacion' => 'bg-warning text-dark',
                                            'Cotizada' => 'bg-primary',
                                            'En_Aprobacion' => 'bg-warning text-dark',
                                            'Aprobada' => 'bg-success',
                                            'En_Adquisicion' => 'bg-primary',
                                            'Completada' => 'bg-dark',
                                            'Rechazada' => 'bg-danger',
                                            'Cancelada' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                        $estadoLabel = str_replace('_', ' ', $solicitud->estado);
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $estadoLabel }}</span>
                                </td>
                                <td>
                                    @php
                                        $prioridadClass = match($solicitud->prioridad) {
                                            'Urgente' => 'danger',
                                            'Alta' => 'warning',
                                            'Media' => 'info',
                                            'Baja' => 'secondary',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $prioridadClass }}">
                                        @if($solicitud->prioridad == 'Urgente')
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        @elseif($solicitud->prioridad == 'Alta')
                                            <i class="bi bi-arrow-up-circle-fill me-1"></i>
                                        @elseif($solicitud->prioridad == 'Media')
                                            <i class="bi bi-dash-circle-fill me-1"></i>
                                        @else
                                            <i class="bi bi-arrow-down-circle-fill me-1"></i>
                                        @endif
                                        {{ $solicitud->prioridad }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($solicitud->monto_total_estimado)
                                        <strong class="text-success">Q {{ number_format($solicitud->monto_total_estimado, 2) }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('solicitudes.show', $solicitud->id_solicitud) }}" 
                                           class="btn btn-outline-primary"
                                           title="Ver detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @if(in_array($solicitud->estado, ['Creada', 'Rechazada']))
                                            <a href="{{ route('solicitudes.edit', $solicitud->id_solicitud) }}" 
                                               class="btn btn-outline-warning"
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                        
                                        @if($solicitud->estado == 'Creada')
                                            <form action="{{ route('solicitudes.enviar-presupuesto', $solicitud->id_solicitud) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Enviar esta solicitud a Presupuesto?');">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-outline-success"
                                                        title="Enviar a Presupuesto">
                                                    <i class="bi bi-send"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3 mb-3">No se encontraron solicitudes</p>
                <a href="{{ route('solicitudes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Crear tu primera solicitud
                </a>
            </div>
        @endif
    </div>
    @if($solicitudes->hasPages())
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Mostrando {{ $solicitudes->firstItem() }} - {{ $solicitudes->lastItem() }} de {{ $solicitudes->total() }} solicitudes
            </small>
            {{ $solicitudes->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
