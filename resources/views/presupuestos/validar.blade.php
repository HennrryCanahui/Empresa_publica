@extends('layouts.app')

@section('title', 'Validar Presupuesto')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>
            <i class="bi bi-cash"></i> 
            Validar Presupuesto - Solicitud #{{ $solicitud->numero_solicitud }}
        </h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('presupuestos.index') }}" class="btn btn-secondary">
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

        <!-- Productos -->
        <div class="card">
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
    </div>

    <div class="col-md-4">
        <!-- Formulario de Validación -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Validación de Presupuesto</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('presupuestos.procesar', $solicitud) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="validacion" class="form-label">Decisión *</label>
                        <select class="form-select @error('validacion') is-invalid @enderror" 
                                id="validacion" name="validacion" required>
                            <option value="">Seleccione...</option>
                            @foreach(['Válido' => 'Válido', 'Requiere_Ajuste' => 'Requiere Ajuste', 'Rechazado' => 'Rechazado'] as $key => $value)
                            <option value="{{ $key }}" {{ old('validacion') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                            @endforeach
                        </select>
                        @error('validacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="partida_presupuestaria" class="form-label">Partida Presupuestaria *</label>
                        <input type="text" class="form-control @error('partida_presupuestaria') is-invalid @enderror" 
                               id="partida_presupuestaria" name="partida_presupuestaria" 
                               value="{{ old('partida_presupuestaria') }}" required>
                        @error('partida_presupuestaria')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="disponibilidad_actual" class="form-label">Disponibilidad Actual *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('disponibilidad_actual') is-invalid @enderror" 
                                   id="disponibilidad_actual" name="disponibilidad_actual" 
                                   value="{{ old('disponibilidad_actual') }}" required>
                            @error('disponibilidad_actual')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                  id="observaciones" name="observaciones" rows="3">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            En caso de rechazo, por favor indique los motivos.
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Validación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
