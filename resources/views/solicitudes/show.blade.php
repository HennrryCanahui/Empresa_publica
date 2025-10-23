@extends('layouts.app')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('solicitudes.mias') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver a Mis Solicitudes
        </a>
    </div>
</div>

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

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    {{ $solicitud->numero_solicitud }}
                </h4>
                @php
                    $badgeClass = match($solicitud->estado) {
                        'Pendiente' => 'bg-warning text-dark',
                        'En Revisión' => 'bg-info',
                        'Aprobada' => 'bg-success',
                        'Rechazada' => 'bg-danger',
                        'Anulada' => 'bg-secondary',
                        'En Proceso' => 'bg-primary',
                        'Completada' => 'bg-dark',
                        default => 'bg-secondary'
                    };
                @endphp
                <span class="badge {{ $badgeClass }} fs-6">{{ $solicitud->estado }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Fecha de Creación</h6>
                        <p class="mb-0">{{ $solicitud->fecha_creacion->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Prioridad</h6>
                        @php
                            $prioridadClass = match($solicitud->prioridad) {
                                'Urgente' => 'text-danger',
                                'Alta' => 'text-warning',
                                'Media' => 'text-info',
                                'Baja' => 'text-secondary',
                                default => 'text-secondary'
                            };
                        @endphp
                        <p class="mb-0 {{ $prioridadClass }} fw-bold">{{ $solicitud->prioridad }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Creado por</h6>
                        <p class="mb-0">{{ $solicitud->usuarioCreador->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Unidad Solicitante</h6>
                        <p class="mb-0">{{ $solicitud->unidadSolicitante->nombre ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($solicitud->fecha_limitie)
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Fecha Límite</h6>
                        <p class="mb-0">
                            {{ \Carbon\Carbon::parse($solicitud->fecha_limitie)->format('d/m/Y') }}
                            @if(\Carbon\Carbon::parse($solicitud->fecha_limitie)->isPast())
                                <span class="badge bg-danger ms-2">Vencida</span>
                            @endif
                        </p>
                    </div>
                @endif

                @if($solicitud->monto_total_estimado)
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Monto Estimado</h6>
                        <p class="mb-0 fs-5 fw-bold text-success">
                            Q {{ number_format($solicitud->monto_total_estimado, 2) }}
                        </p>
                    </div>
                @endif

                <hr>

                <div class="mb-3">
                    <h6 class="text-muted mb-2">Descripción</h6>
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $solicitud->descripcion }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-2">Justificación</h6>
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $solicitud->justificacion }}</p>
                </div>
            </div>
        </div>

        <!-- Historial Reciente -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial Reciente</h5>
                <a href="{{ route('solicitudes.historial', $solicitud) }}" class="btn btn-sm btn-outline-primary">
                    Ver Todo
                </a>
            </div>
            <div class="card-body">
                @if($solicitud->historialEstados->isEmpty())
                    <p class="text-muted text-center mb-0">No hay historial disponible</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($solicitud->historialEstados->take(5) as $historial)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        @if($historial->estado_anterior)
                                            <span class="badge bg-secondary">{{ $historial->estado_anterior }}</span>
                                            <i class="bi bi-arrow-right mx-2"></i>
                                        @endif
                                        <span class="badge bg-primary">{{ $historial->estado_nuevo }}</span>
                                        
                                        @if($historial->observaciones)
                                            <p class="mb-1 mt-2 text-muted small">{{ $historial->observaciones }}</p>
                                        @endif
                                        
                                        <small class="text-muted">
                                            Por {{ $historial->usuario->name ?? 'Sistema' }}
                                        </small>
                                    </div>
                                    <small class="text-muted text-nowrap">
                                        {{ $historial->fecha_cambio->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar con acciones -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Acciones</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(in_array($solicitud->estado, ['Pendiente', 'Rechazada']) && $solicitud->id_usuario_creador == Auth::id())
                        <a href="{{ route('solicitudes.edit', $solicitud) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Editar Solicitud
                        </a>
                    @endif

                    @if(!in_array($solicitud->estado, ['Completada', 'Finalizada', 'Anulada']) && $solicitud->id_usuario_creador == Auth::id())
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#anularModal">
                            <i class="bi bi-x-circle me-2"></i>Anular Solicitud
                        </button>
                    @endif

                    @if(in_array($solicitud->estado, ['Anulada', 'Rechazada']) && $solicitud->id_usuario_creador == Auth::id())
                        <form action="{{ route('solicitudes.reabrir', $solicitud) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('¿Está seguro de reabrir esta solicitud?')">
                                <i class="bi bi-arrow-clockwise me-2"></i>Reabrir Solicitud
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('solicitudes.historial', $solicitud) }}" class="btn btn-outline-info">
                        <i class="bi bi-clock-history me-2"></i>Ver Historial Completo
                    </a>

                    <a href="{{ route('solicitudes.mias') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver al Listado
                    </a>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <strong>Creada:</strong> {{ $solicitud->created_at->format('d/m/Y H:i') }}<br>
                    <strong>Última actualización:</strong> {{ $solicitud->updated_at->format('d/m/Y H:i') }}
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Anular -->
<div class="modal fade" id="anularModal" tabindex="-1" aria-labelledby="anularModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('solicitudes.anular', $solicitud) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="anularModalLabel">Anular Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Esta acción anulará la solicitud. Podrá reabrirla posteriormente si lo desea.
                    </div>
                    <div class="mb-3">
                        <label for="motivo_anulacion" class="form-label">Motivo de Anulación <span class="text-danger">*</span></label>
                        <textarea name="motivo_anulacion" 
                                  id="motivo_anulacion" 
                                  class="form-control" 
                                  rows="4" 
                                  required
                                  placeholder="Explique el motivo de la anulación..."></textarea>
                        <small class="text-muted">Mínimo 10 caracteres</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-2"></i>Anular Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection