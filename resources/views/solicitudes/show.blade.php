@extends('layouts.app')

@section('title', 'Detalle de Solicitud')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>
            <i class="bi bi-file-text"></i> 
            Solicitud #{{ $solicitud->numero_solicitud }}
        </h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Listado
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Información General -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Información General</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Estado:</strong>
                        <span class="badge bg-{{ $estados_color[$solicitud->estado] ?? 'secondary' }}">
                            {{ $solicitud->estado }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha de Creación:</strong>
                        {{ $solicitud->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Unidad Solicitante:</strong>
                        {{ $solicitud->unidadSolicitante->nombre }}
                    </div>
                    <div class="col-md-6">
                        <strong>Solicitante:</strong>
                        {{ $solicitud->usuarioCreador->nombre }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Prioridad:</strong>
                        <span class="badge bg-{{ $prioridades_color[$solicitud->prioridad] ?? 'secondary' }}">
                            {{ $solicitud->prioridad }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha Requerida:</strong>
                        {{ $solicitud->fecha_requerida->format('d/m/Y') }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Descripción:</strong>
                        <p class="mb-0">{{ $solicitud->descripcion }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <strong>Justificación:</strong>
                        <p class="mb-0">{{ $solicitud->justificacion }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Productos Solicitados</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Est.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitud->detalles as $detalle)
                            <tr>
                                <td>
                                    <strong>{{ $detalle->producto->nombre }}</strong>
                                    @if($detalle->especificaciones_adicionales)
                                    <br>
                                    <small class="text-muted">
                                        {{ $detalle->especificaciones_adicionales }}
                                    </small>
                                    @endif
                                </td>
                                <td>
                                    {{ number_format($detalle->cantidad, 2) }}
                                    {{ $detalle->producto->unidad_medida }}
                                </td>
                                <td>{{ number_format($detalle->precio_estimado_unitario, 2) }}</td>
                                <td>{{ number_format($detalle->precio_estimado_total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total Estimado:</strong></td>
                                <td><strong>{{ number_format($solicitud->monto_estimado_total, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Historial -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Historial de la Solicitud</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($solicitud->historial()->orderBy('fecha_cambio', 'desc')->get() as $historial)
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">
                                {{ $historial->estado_nuevo }}
                                <small class="text-muted">
                                    {{ $historial->fecha_cambio->format('d/m/Y H:i') }}
                                </small>
                            </h6>
                            <p class="mb-0">
                                {{ $historial->usuario->nombre }}
                                @if($historial->observaciones)
                                <br>
                                <small>{{ $historial->observaciones }}</small>
                                @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Acciones -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Acciones Disponibles</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($solicitud->estado == 'Borrador' && Auth::id() == $solicitud->id_usuario_creador)
                    <a href="{{ route('solicitudes.edit', $solicitud) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar Solicitud
                    </a>
                    @endif

                    @can('validar-presupuesto')
                    @if($solicitud->estado == 'Pendiente')
                    <a href="{{ route('presupuestos.validar', $solicitud) }}" class="btn btn-warning">
                        <i class="bi bi-cash"></i> Validar Presupuesto
                    </a>
                    @endif
                    @endcan

                    @can('crear-cotizaciones')
                    @if($solicitud->estado == 'Presupuesto Validado')
                    <a href="{{ route('cotizaciones.create', $solicitud) }}" class="btn btn-success">
                        <i class="bi bi-receipt"></i> Crear Cotización
                    </a>
                    @endif
                    @endcan

                    @can('evaluar-solicitudes')
                    @if($solicitud->estado == 'Cotizada')
                    <a href="{{ route('aprobaciones.evaluar', $solicitud) }}" class="btn btn-info">
                        <i class="bi bi-check-circle"></i> Evaluar Solicitud
                    </a>
                    @endif
                    @endcan

                    @can('crear-adquisiciones')
                    @if($solicitud->estado == 'Aprobada')
                    <a href="{{ route('adquisiciones.create', $solicitud) }}" class="btn btn-success">
                        <i class="bi bi-bag"></i> Generar Orden de Compra
                    </a>
                    @endif
                    @endcan
                </div>
            </div>
        </div>

        <!-- Documentos -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Documentos Adjuntos</h5>
            </div>
            <div class="card-body">
                @if($solicitud->documentos->count() > 0)
                <div class="list-group">
                    @foreach($solicitud->documentos as $documento)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{ route('documentos.download', $documento) }}" target="_blank">
                            <i class="bi bi-file-earmark"></i> {{ $documento->nombre_archivo }}
                        </a>
                        <small class="text-muted">
                            {{ number_format($documento->tamano_bytes / 1024, 2) }} KB
                        </small>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted mb-0">No hay documentos adjuntos</p>
                @endif
            </div>
        </div>

        <!-- Información Adicional -->
        @if($solicitud->presupuesto)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Información de Presupuesto</h5>
            </div>
            <div class="card-body">
                <p><strong>Validación:</strong> {{ $solicitud->presupuesto->validacion }}</p>
                <p><strong>Partida:</strong> {{ $solicitud->presupuesto->partida_presupuestaria }}</p>
                <p><strong>Disponibilidad:</strong> 
                    {{ number_format($solicitud->presupuesto->disponibilidad_actual, 2) }}
                </p>
                @if($solicitud->presupuesto->observaciones)
                <p><strong>Observaciones:</strong><br>
                    {{ $solicitud->presupuesto->observaciones }}
                </p>
                @endif
            </div>
        </div>
        @endif

        @if($solicitud->aprobacion)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información de Aprobación</h5>
            </div>
            <div class="card-body">
                <p><strong>Decisión:</strong> {{ $solicitud->aprobacion->decision }}</p>
                <p><strong>Monto Aprobado:</strong> 
                    {{ number_format($solicitud->aprobacion->monto_aprobado, 2) }}
                </p>
                @if($solicitud->aprobacion->observaciones)
                <p><strong>Observaciones:</strong><br>
                    {{ $solicitud->aprobacion->observaciones }}
                </p>
                @endif
            </div>
        </div>
        @endif
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

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background: #0d6efd;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #0d6efd;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 7px;
    top: 15px;
    height: calc(100% + 5px);
    width: 2px;
    background: #0d6efd;
}

.timeline-title {
    margin: 0;
    font-size: 1rem;
}
</style>
@endsection
