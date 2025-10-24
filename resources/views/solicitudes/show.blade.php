@extends('layouts.app')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0">
            <i class="bi bi-file-earmark-text me-2"></i>Detalle de Solicitud
        </h2>
        <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>
@endsection

@section('content')
<!-- Alertas -->
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
    <!-- Columna principal -->
    <div class="col-lg-8">
        <!-- Información principal -->
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $solicitud->numero_solicitud }}</h4>
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
                <span class="badge {{ $badgeClass }} fs-6">{{ $estadoLabel }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2"><i class="bi bi-calendar3 me-2"></i>Fecha de Creación</h6>
                        <p class="mb-0">{{ $solicitud->fecha_creacion->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2"><i class="bi bi-flag me-2"></i>Prioridad</h6>
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

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2"><i class="bi bi-person me-2"></i>Creado por</h6>
                        <p class="mb-0">
                            {{ $solicitud->usuarioCreador->nombre ?? 'N/A' }} 
                            {{ $solicitud->usuarioCreador->apellido ?? '' }}
                        </p>
                        <small class="text-muted">{{ $solicitud->usuarioCreador->rol ?? '' }}</small>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2"><i class="bi bi-building me-2"></i>Unidad Solicitante</h6>
                        <p class="mb-0">{{ $solicitud->unidadSolicitante->nombre ?? 'N/A' }}</p>
                        <small class="text-muted">{{ $solicitud->unidadSolicitante->tipo ?? '' }}</small>
                    </div>
                </div>

                @if($solicitud->fecha_limite)
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-muted mb-2"><i class="bi bi-clock me-2"></i>Fecha Límite</h6>
                        <p class="mb-0">{{ \Carbon\Carbon::parse($solicitud->fecha_limite)->format('d/m/Y') }}</p>
                    </div>
                </div>
                @endif

                <hr>

                <div class="mb-3">
                    <h6 class="text-muted mb-2"><i class="bi bi-file-text me-2"></i>Descripción</h6>
                    <p>{{ $solicitud->descripcion }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-2"><i class="bi bi-chat-left-text me-2"></i>Justificación</h6>
                    <p>{{ $solicitud->justificacion }}</p>
                </div>
            </div>
        </div>

        <!-- Productos solicitados -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>Productos/Servicios Solicitados</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit. Est.</th>
                                <th>Total Est.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitud->detalles as $detalle)
                                <tr>
                                    <td>
                                        <strong>{{ $detalle->producto->nombre ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $detalle->producto->codigo ?? '' }}</small>
                                        @if($detalle->especificaciones_adicionales)
                                            <br>
                                            <small class="text-info">
                                                <i class="bi bi-info-circle me-1"></i>
                                                {{ $detalle->especificaciones_adicionales }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $detalle->cantidad }} 
                                        <small class="text-muted">{{ $detalle->producto->unidad_medida ?? '' }}</small>
                                    </td>
                                    <td>Q {{ number_format($detalle->precio_estimado_unitario ?? 0, 2) }}</td>
                                    <td class="fw-bold">Q {{ number_format($detalle->precio_estimado_total ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Estimado:</th>
                                <th>Q {{ number_format($solicitud->monto_total_estimado ?? 0, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Información de Presupuesto -->
        @if($solicitud->presupuesto)
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Validación Presupuestaria</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Validación</h6>
                        @php
                            $validacionBadge = match($solicitud->presupuesto->validacion) {
                                'Válido' => 'bg-success',
                                'Rechazado' => 'bg-danger',
                                'Requiere_Ajuste' => 'bg-warning text-dark',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $validacionBadge }}">
                            {{ str_replace('_', ' ', $solicitud->presupuesto->validacion) }}
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Partida Presupuestaria</h6>
                        <p class="mb-0">{{ $solicitud->presupuesto->partida_presupuestaria }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Monto Estimado</h6>
                        <p class="mb-0">Q {{ number_format($solicitud->presupuesto->monto_estimado, 2) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Disponibilidad Actual</h6>
                        <p class="mb-0">Q {{ number_format($solicitud->presupuesto->disponibilidad_actual, 2) }}</p>
                    </div>
                    @if($solicitud->presupuesto->observaciones)
                    <div class="col-12">
                        <h6 class="text-muted mb-1">Observaciones</h6>
                        <p class="mb-0">{{ $solicitud->presupuesto->observaciones }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Cotizaciones -->
        @if($solicitud->cotizaciones && $solicitud->cotizaciones->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Cotizaciones</h5>
            </div>
            <div class="card-body">
                @foreach($solicitud->cotizaciones as $cotizacion)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1">{{ $cotizacion->numero_cotizacion }}</h6>
                                <p class="mb-0 text-muted">
                                    {{ $cotizacion->proveedor->razon_social ?? 'N/A' }}
                                </p>
                            </div>
                            @php
                                $estadoCotBadge = match($cotizacion->estado) {
                                    'Activa' => 'bg-info',
                                    'Seleccionada' => 'bg-success',
                                    'Descartada' => 'bg-secondary',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $estadoCotBadge }}">{{ $cotizacion->estado }}</span>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">Monto Total</small>
                                <p class="mb-0 fw-bold">Q {{ number_format($cotizacion->monto_total, 2) }}</p>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Tiempo de Entrega</small>
                                <p class="mb-0">{{ $cotizacion->tiempo_entrega_dias }} días</p>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Fecha Cotización</small>
                                <p class="mb-0">{{ \Carbon\Carbon::parse($cotizacion->fecha_cotizacion)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Aprobación -->
        @if($solicitud->aprobacion)
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-check2-circle me-2"></i>Aprobación</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Decisión</h6>
                        @php
                            $decisionBadge = match($solicitud->aprobacion->decision) {
                                'Aprobada' => 'bg-success',
                                'Rechazada' => 'bg-danger',
                                'Requiere_Revision' => 'bg-warning text-dark',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $decisionBadge }}">
                            {{ str_replace('_', ' ', $solicitud->aprobacion->decision) }}
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Monto Aprobado</h6>
                        <p class="mb-0">Q {{ number_format($solicitud->aprobacion->monto_aprobado, 2) }}</p>
                    </div>
                    <div class="col-12">
                        <h6 class="text-muted mb-1">Observaciones</h6>
                        <p class="mb-0">{{ $solicitud->aprobacion->observaciones }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Adquisición -->
        @if($solicitud->adquisicion)
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-bag-check me-2"></i>Orden de Compra</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Número de Orden</h6>
                        <p class="mb-0 fw-bold">{{ $solicitud->adquisicion->numero_orden_compra }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Estado de Entrega</h6>
                        @php
                            $entregaBadge = match($solicitud->adquisicion->estado_entrega) {
                                'Pendiente' => 'bg-warning text-dark',
                                'Parcial' => 'bg-info',
                                'Completa' => 'bg-success',
                                'Cancelada' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $entregaBadge }}">{{ $solicitud->adquisicion->estado_entrega }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Monto Final</h6>
                        <p class="mb-0">Q {{ number_format($solicitud->adquisicion->monto_final, 2) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Fecha de Adquisición</h6>
                        <p class="mb-0">{{ \Carbon\Carbon::parse($solicitud->adquisicion->fecha_adquisicion)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Columna lateral -->
    <div class="col-lg-4">
        <!-- Acciones rápidas -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Acciones</h6>
            </div>
            <div class="card-body">
                @if(in_array($solicitud->estado, ['Creada', 'Rechazada']) && 
                    $solicitud->id_usuario_creador == Auth::user()->id_usuario)
                    <a href="{{ route('solicitudes.edit', $solicitud->id_solicitud) }}" class="btn btn-warning w-100 mb-2">
                        <i class="bi bi-pencil me-2"></i>Editar Solicitud
                    </a>
                @endif

                @if($solicitud->estado == 'Creada' && 
                    $solicitud->id_usuario_creador == Auth::user()->id_usuario)
                    <form action="{{ route('solicitudes.enviar-presupuesto', $solicitud->id_solicitud) }}" 
                          method="POST"
                          onsubmit="return confirm('¿Enviar esta solicitud a Presupuesto?');">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-send me-2"></i>Enviar a Presupuesto
                        </button>
                    </form>
                @endif

                @if(in_array($solicitud->estado, ['Creada', 'En_Presupuesto']) && 
                    $solicitud->id_usuario_creador == Auth::user()->id_usuario)
                    <button type="button" class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#modalCancelar">
                        <i class="bi bi-x-circle me-2"></i>Cancelar Solicitud
                    </button>
                @endif

                <a href="{{ route('solicitudes.historial', $solicitud->id_solicitud) }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-clock-history me-2"></i>Ver Historial
                </a>
            </div>
        </div>

        <!-- Historial reciente -->
        @if($solicitud->historialEstados && $solicitud->historialEstados->count() > 0)
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial Reciente</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($solicitud->historialEstados->take(5) as $historial)
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 32px; height: 32px;">
                                        <i class="bi bi-arrow-right-short"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1 small">
                                        <strong>{{ str_replace('_', ' ', $historial->estado_nuevo) }}</strong>
                                    </p>
                                    <p class="mb-0 text-muted small">
                                        {{ $historial->usuario->nombre ?? 'Sistema' }}
                                        <br>
                                        {{ $historial->fecha_cambio->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Cancelar -->
<div class="modal fade" id="modalCancelar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('solicitudes.cancelar', $solicitud->id_solicitud) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cancelar Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Esta acción cancelará permanentemente la solicitud.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo de Cancelación <span class="text-danger">*</span></label>
                        <textarea name="motivo" class="form-control" rows="3" required 
                                  placeholder="Explica el motivo de la cancelación..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Cancelar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
