@extends('layouts.app')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('solicitudes.show', $solicitud) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver a la Solicitud
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Historial de Estados - {{ $solicitud->numero_solicitud }}
                </h4>
            </div>
            <div class="card-body">
                @if($historial->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-clock" style="font-size: 4rem; color: #6c757d;"></i>
                        <h5 class="text-muted mt-3">No hay historial disponible</h5>
                        <p class="text-muted">Los cambios de estado aparecerán aquí</p>
                    </div>
                @else
                    <div class="timeline">
                        @foreach($historial as $index => $item)
                            <div class="timeline-item {{ $index === 0 ? 'timeline-item-latest' : '' }}">
                                <div class="timeline-marker">
                                    <i class="bi bi-circle-fill {{ $index === 0 ? 'text-primary' : 'text-secondary' }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="card mb-3 {{ $index === 0 ? 'border-primary' : '' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1">
                                                        @if($item->estado_anterior)
                                                            <span class="badge bg-secondary">{{ $item->estado_anterior }}</span>
                                                            <i class="bi bi-arrow-right mx-2"></i>
                                                        @endif
                                                        <span class="badge bg-primary">{{ $item->estado_nuevo }}</span>
                                                    </h6>
                                                </div>
                                                <small class="text-muted text-nowrap ms-3">
                                                    {{ $item->fecha_cambio->format('d/m/Y H:i') }}
                                                </small>
                                            </div>

                                            @if($item->observaciones)
                                                <div class="alert alert-light mb-2">
                                                    <i class="bi bi-chat-left-text me-2"></i>
                                                    <strong>Observaciones:</strong>
                                                    <p class="mb-0 mt-1">{{ $item->observaciones }}</p>
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center text-muted small">
                                                <span>
                                                    <i class="bi bi-person me-1"></i>
                                                    {{ $item->usuario->name ?? 'Sistema' }}
                                                </span>
                                                <span>
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $item->fecha_cambio->diffForHumans() }}
                                                </span>
                                            </div>

                                            @if($item->ip_address)
                                                <small class="text-muted">
                                                    <i class="bi bi-globe me-1"></i>IP: {{ $item->ip_address }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 20px;
    bottom: -20px;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 5px;
}

.timeline-marker i {
    font-size: 1.2rem;
}

.timeline-content {
    flex: 1;
}

.timeline-item-latest .card {
    box-shadow: 0 0 15px rgba(13, 110, 253, 0.2);
}
</style>
@endsection