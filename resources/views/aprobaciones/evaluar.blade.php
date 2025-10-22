@extends('layouts.app')

@section('title', 'Evaluar Solicitud')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>
            <i class="bi bi-check-circle"></i> 
            Evaluar Solicitud #{{ $solicitud->numero_solicitud }}
        </h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('aprobaciones.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Listado
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Información de la Solicitud -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Información de la Solicitud</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Unidad Solicitante:</strong>
                        <p class="mb-0">{{ $solicitud->unidadSolicitante->nombre }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha de Solicitud:</strong>
                        <p class="mb-0">{{ $solicitud->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Descripción:</strong>
                        <p class="mb-0">{{ $solicitud->descripcion }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Justificación:</strong>
                        <p class="mb-0">{{ $solicitud->justificacion }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Prioridad:</strong>
                        <span class="badge bg-{{ $prioridades_color[$solicitud->prioridad] ?? 'secondary' }}">
                            {{ $solicitud->prioridad }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha Requerida:</strong>
                        <p class="mb-0">{{ $solicitud->fecha_requerida->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Presupuesto -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Información de Presupuesto</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Validación:</strong>
                        <span class="badge bg-{{ $validaciones_color[$solicitud->presupuesto->validacion] ?? 'secondary' }}">
                            {{ $solicitud->presupuesto->validacion }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Monto Disponible:</strong>
                        <p class="mb-0">${{ number_format($solicitud->presupuesto->disponibilidad_actual, 2) }}</p>
                    </div>
                </div>
                
                @if($solicitud->presupuesto->observaciones)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <strong>Observaciones:</strong>
                        <p class="mb-0">{{ $solicitud->presupuesto->observaciones }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Cotizaciones -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Cotizaciones Recibidas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Proveedor</th>
                                <th>Monto</th>
                                <th>T. Entrega</th>
                                <th>Cond. Pago</th>
                                <th>Documento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitud->cotizaciones as $cotizacion)
                            <tr>
                                <td>{{ $cotizacion->proveedor->razon_social }}</td>
                                <td>${{ number_format($cotizacion->monto_total, 2) }}</td>
                                <td>{{ $cotizacion->tiempo_entrega }} días</td>
                                <td>{{ $cotizacion->condiciones_pago }}</td>
                                <td>
                                    <a href="{{ route('cotizaciones.download', $cotizacion) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       target="_blank">
                                        <i class="bi bi-file-pdf"></i> Ver PDF
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-3">
                    <a href="{{ route('cotizaciones.comparar', $solicitud) }}" class="btn btn-info">
                        <i class="bi bi-table"></i> Ver Comparativa
                    </a>
                </div>
            </div>
        </div>

        <!-- Documentos -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Documentos Adjuntos</h5>
            </div>
            <div class="card-body">
                @if($solicitud->documentos->count() > 0)
                <div class="list-group">
                    @foreach($solicitud->documentos as $documento)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="bi bi-file-earmark"></i> {{ $documento->nombre_archivo }}
                            </h6>
                            <small>{{ number_format($documento->tamano_bytes / 1024, 2) }} KB</small>
                        </div>
                        <p class="mb-1">{{ $documento->tipo_documento }}</p>
                        <a href="{{ route('documentos.download', $documento) }}" class="btn btn-sm btn-link">
                            <i class="bi bi-download"></i> Descargar
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted mb-0">No hay documentos adjuntos</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Formulario de Aprobación -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Formulario de Aprobación</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('aprobaciones.procesar', $solicitud) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="decision" class="form-label">Decisión *</label>
                        <select class="form-select @error('decision') is-invalid @enderror" 
                                id="decision" name="decision" required>
                            <option value="">Seleccione...</option>
                            @foreach(['APROBADA' => 'Aprobar', 'RECHAZADA' => 'Rechazar'] as $key => $value)
                            <option value="{{ $key }}" {{ old('decision') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                            @endforeach
                        </select>
                        @error('decision')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="monto_aprobado" class="form-label">Monto Aprobado *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('monto_aprobado') is-invalid @enderror" 
                                   id="monto_aprobado" name="monto_aprobado" 
                                   value="{{ old('monto_aprobado', $solicitud->monto_estimado_total) }}" required>
                            @error('monto_aprobado')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">
                            Monto estimado: ${{ number_format($solicitud->monto_estimado_total, 2) }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="condiciones_aprobacion" class="form-label">Condiciones de Aprobación</label>
                        <textarea class="form-control @error('condiciones_aprobacion') is-invalid @enderror" 
                                  id="condiciones_aprobacion" name="condiciones_aprobacion" 
                                  rows="3">{{ old('condiciones_aprobacion') }}</textarea>
                        @error('condiciones_aprobacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Especifique cualquier condición o restricción para la aprobación.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                  id="observaciones" name="observaciones" 
                                  rows="3">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            En caso de rechazo, indique los motivos detalladamente.
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Registrar Decisión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
